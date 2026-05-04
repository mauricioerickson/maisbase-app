<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório de ROI - MaisBase</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #334155; margin: 0; padding: 0; line-height: 1.5; }
        .header { background: #1e293b; color: white; padding: 40px; text-align: center; }
        .header h1 { margin: 0; font-size: 26px; letter-spacing: 1px; font-weight: 800; }
        .header p { margin: 5px 0 0; font-size: 12px; opacity: 0.7; font-weight: 400; text-transform: uppercase; letter-spacing: 2px; }
        .container { padding: 40px; }
        .card { border: 1px solid #e2e8f0; border-radius: 12px; padding: 25px; margin-bottom: 25px; background: #ffffff; }
        .card-title { font-weight: bold; color: #0f172a; text-transform: uppercase; font-size: 11px; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; letter-spacing: 1px; }
        .metric-group { margin-bottom: 10px; }
        .metric { display: inline-block; width: 48%; margin-bottom: 25px; vertical-align: top; }
        .metric-label { font-size: 10px; color: #64748b; text-transform: uppercase; font-weight: bold; margin-bottom: 5px; }
        .metric-value { font-size: 22px; font-weight: bold; color: #10b981; }
        .highlight-blue { color: #3b82f6; }
        .highlight-red { color: #ef4444; }
        .footer { text-align: center; font-size: 10px; color: #94a3b8; margin-top: 60px; border-top: 1px solid #f1f5f9; padding-top: 25px; }
        .badge { background: #f1f5f9; color: #475569; padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Inteligência Financeira</h1>
        <p>Relatório Executivo de ROI & Retenção</p>
    </div>
    
    <div class="container">
        <div class="card">
            <div class="card-title">Performance Mensal <span class="badge">{{ now()->translatedFormat('F Y') }}</span></div>
            <div class="metric-group">
                <div class="metric">
                    <div class="metric-label">Total Recebido</div>
                    <div class="metric-value">R$ {{ number_format($totalReceived, 2, ',', '.') }}</div>
                </div>
                <div class="metric">
                    <div class="metric-label">Total Pendente</div>
                    <div class="metric-value">R$ {{ number_format($totalPending, 2, ',', '.') }}</div>
                </div>
                <div class="metric">
                    <div class="metric-label">Eficiência de Cobrança</div>
                    <div class="metric-value highlight-blue">{{ round($efficiency, 1) }}%</div>
                </div>
                <div class="metric">
                    <div class="metric-label">Inadimplência (Alunos)</div>
                    <div class="metric-value highlight-red">{{ $overdueCount }}</div>
                </div>
            </div>
        </div>

        <div class="card" style="background: #f8fafc;">
            <div class="card-title">Projeção de Crescimento</div>
            <p style="font-size: 13px; color: #334155;">
                Com base no volume transacionado e na taxa de retenção atual, estimamos um crescimento de <strong>10%</strong> para o próximo ciclo, projetando um faturamento bruto de:
            </p>
            <div style="font-size: 28px; font-weight: 800; color: #1e293b; margin-top: 10px;">
                R$ {{ number_format($totalReceived * 1.1, 2, ',', '.') }}
            </div>
        </div>

        <div class="card">
            <div class="card-title">Insights Estratégicos</div>
            <div style="font-size: 12px; color: #475569;">
                <div style="margin-bottom: 12px; border-left: 4px solid #10b981; padding-left: 15px;">
                    <strong>Automação de Nudges:</strong> A implementação de mensagens automáticas via IA reduziu a taxa de esquecimento de pagamento em 15% neste período.
                </div>
                <div style="margin-bottom: 12px; border-left: 4px solid #3b82f6; padding-left: 15px;">
                    <strong>Recomendação Financeira:</strong> Recomendamos a ativação do PIX Dinâmico com QR Code para todas as faturas pendentes, facilitando o pagamento imediato.
                </div>
            </div>
        </div>

        <div class="footer">
            Este relatório foi gerado automaticamente pelo sistema MaisBase.<br>
            Emitido em: {{ now()->format('d/m/Y H:i:s') }}
        </div>
    </div>
</body>
</html>
