require('dotenv').config();
const fs = require('fs');
const path = require('path');
const express = require('express');
const { 
    default: makeWASocket, 
    useMultiFileAuthState, 
    DisconnectReason,
    fetchLatestBaileysVersion,
    makeCacheableSignalKeyStore
} = require('@whiskeysockets/baileys');
const pino = require('pino');
const { Boom } = require('@hapi/boom');
const qrcode = require('qrcode-terminal');
const cors = require('cors');

const app = express();
app.use(express.json());
app.use(cors());

const sessions = new Map();
const logger = pino({ level: 'silent' });

/**
 * Inicia uma sessão Baileys para um Tenant específico.
 */
async function startSession(tenantId) {
    if (sessions.has(tenantId)) return sessions.get(tenantId);

    const { state, saveCreds } = await useMultiFileAuthState(`./auth_info_${tenantId}`);
    const { version } = await fetchLatestBaileysVersion();

    const sock = makeWASocket({
        version,
        logger,
        auth: {
            creds: state.creds,
            keys: makeCacheableSignalKeyStore(state.keys, logger),
        },
        browser: ['MaisBase', 'Chrome', '1.0.0'],
        markOnlineOnConnect: true,
    });

    sessions.set(tenantId, { sock, qr: null, status: 'connecting' });

    sock.ev.on('connection.update', (update) => {
        const { connection, lastDisconnect, qr } = update;
        
        if (qr) {
            console.log(`[Session ${tenantId}] QR Code received`);
            sessions.get(tenantId).qr = qr;
        }

        if (connection === 'close') {
            const shouldReconnect = (lastDisconnect.error instanceof Boom) 
                ? lastDisconnect.error?.output?.statusCode !== DisconnectReason.loggedOut 
                : true;
            
            console.log(`[Session ${tenantId}] Connection closed. Reconnecting: ${shouldReconnect}`);
            
            sessions.delete(tenantId); // Limpa a sessão antiga antes de reconectar

            if (shouldReconnect) {
                startSession(tenantId);
            }
        } else if (connection === 'open') {
            console.log(`[Session ${tenantId}] Connection opened`);
            sessions.get(tenantId).status = 'connected';
            sessions.get(tenantId).qr = null;
        }
    });

    sock.ev.on('creds.update', saveCreds);

    return sessions.get(tenantId);
}

// API Endpoints

app.get('/status/:tenantId', async (req, res) => {
    const { tenantId } = req.params;
    const session = sessions.get(tenantId);
    
    if (!session) {
        return res.json({ status: 'disconnected' });
    }

    res.json({ status: session.status, hasQr: !!session.qr });
});

app.get('/qr/:tenantId', async (req, res) => {
    const { tenantId } = req.params;
    let session = sessions.get(tenantId);

    if (!session) {
        session = await startSession(tenantId);
    }

    if (session.status === 'connected') {
        return res.json({ status: 'connected' });
    }

    if (session.qr) {
        return res.json({ status: 'qr', qr: session.qr });
    }

    res.json({ status: 'waiting_qr' });
});

// ... (existing code up to line 105)

app.delete('/session/:tenantId', async (req, res) => {
    const { tenantId } = req.params;
    const session = sessions.get(tenantId);

    if (session) {
        try {
            await session.sock.logout();
        } catch (e) {}
        sessions.delete(tenantId);
    }

    // Limpa a pasta de autenticação para permitir novo QR Code
    const authFolder = path.join(__dirname, `auth_info_${tenantId}`);
    if (fs.existsSync(authFolder)) {
        fs.rmSync(authFolder, { recursive: true, force: true });
    }

    res.json({ success: true, message: 'Session reset successful' });
});

app.post('/send', async (req, res) => {
    const { tenantId, to, message } = req.body;
    const session = sessions.get(tenantId);

    if (!session || session.status !== 'connected') {
        return res.status(400).json({ error: 'Session not connected' });
    }

    try {
        const jid = to.replace(/\D/g, '') + '@s.whatsapp.net';
        await session.sock.sendMessage(jid, { text: message });
        res.json({ success: true });
    } catch (err) {
        res.status(500).json({ error: err.message });
    }
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
    console.log(`WhatsApp Bridge running on port ${PORT}`);
});
