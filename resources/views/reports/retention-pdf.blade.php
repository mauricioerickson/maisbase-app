<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório de Retenção e Risco de Evasão - MaisBase</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #334155; margin: 0; padding: 0; line-height: 1.5; }
        .header { background: #0f172a; color: white; padding: 40px; text-align: center; }
        .header h1 { margin: 0; font-size: 26px; letter-spacing: 1px; font-weight: 800; }
        .header p { margin: 5px 0 0; font-size: 12px; opacity: 0.7; font-weight: 400; text-transform: uppercase; letter-spacing: 2px; }
        .container { padding: 40px; }
        .section-title { font-size: 14px; font-weight: bold; color: #1e293b; text-transform: uppercase; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 8px; }
        .card { border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 25px; background: #ffffff; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { text-align: left; font-size: 10px; color: #64748b; text-transform: uppercase; padding: 12px; border-bottom: 1px solid #e2e8f0; }
        td { padding: 12px; border-bottom: 1px solid #f1f5f9; font-size: 12px; color: #334155; }
        .risk-high { color: #ef4444; font-weight: bold; }
        .risk-medium { color: #f59e0b; font-weight: bold; }
        .badge { background: #fef2f2; color: #ef4444; padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; }
        .footer { text-align: center; font-size: 10px; color: #94a3b8; margin-top: 60px; border-top: 1px solid #f1f5f9; padding-top: 25px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Análise de Retenção & Churn</h1>
        <p>Relatório de Inteligência Preditiva</p>
    </div>
    
    <div class="container">
        <div class="section-title">Resumo Operacional</div>
        <div class="card" style="display: flex; justify-content: space-between;">
            <div style="display: inline-block; width: 30%;">
                <div style="font-size: 10px; color: #64748b; text-transform: uppercase;">Total de Atletas</div>
                <div style="font-size: 24px; font-weight: bold; color: #0f172a;">{{ $totalAthletes }}</div>
            </div>
            <div style="display: inline-block; width: 30%;">
                <div style="font-size: 10px; color: #64748b; text-transform: uppercase;">Atletas em Risco</div>
                <div style="font-size: 24px; font-weight: bold; color: #ef4444;">{{ $atRiskCount }}</div>
            </div>
            <div style="display: inline-block; width: 30%;">
                <div style="font-size: 10px; color: #64748b; text-transform: uppercase;">Taxa de Risco</div>
                <div style="font-size: 24px; font-weight: bold; color: #3b82f6;">{{ $totalAthletes > 0 ? round(($atRiskCount / $totalAthletes) * 100, 1) : 0 }}%</div>
            </div>
        </div>

        <div class="section-title">Detalhamento de Atletas com Alto Risco</div>
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Atleta</th>
                        <th>Responsável</th>
                        <th>Risco (0-100)</th>
                        <th>Última Presença</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($atRiskAthletes as $athlete)
                        <tr>
                            <td style="font-weight: bold;">{{ $athlete->name }}</td>
                            <td>{{ $athlete->guardian->name }}</td>
                            <td>
                                <span class="{{ $athlete->risk_score > 70 ? 'risk-high' : 'risk-medium' }}">
                                    {{ $athlete->risk_score }}%
                                </span>
                            </td>
                            <td>{{ $athlete->attendances()->latest('date')->first()?->date->format('d/m/Y') ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="section-title">Plano de Ação Recomendado</div>
        <div class="card" style="background: #f8fafc;">
            <div style="font-size: 12px; color: #475569;">
                <p><strong>1. Reengajamento imediato:</strong> Disparar Nudges de "Saudades" para os top 5 atletas em risco via WhatsApp.</p>
                <p><strong>2. Auditoria de Presença:</strong> Verificar se as faltas são justificadas por motivos de saúde ou transporte.</p>
                <p><strong>3. Incentivo de Renovação:</strong> Oferecer um benefício exclusivo para os atletas que normalizarem a frequência nos próximos 15 dias.</p>
            </div>
        </div>

        <div class="footer">
            Gerado automaticamente por MaisBase AI Intelligence Engine.<br>
            Emitido em: {{ now()->format('d/m/Y H:i:s') }}
        </div>
    </div>
</body>
</html>
