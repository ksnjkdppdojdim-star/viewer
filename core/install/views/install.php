<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation | Viewer CMS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #3b82f6;
            --bg: #09090b;
            --window-bg: #18181b;
            --titlebar-bg: #27272a;
            --border: #3f3f46;
            --text: #ffffff;
            --text-dim: #a1a1aa;
            --success: #10b981;
            --error: #ef4444;
        }

        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background: radial-gradient(circle at center, #18181b 0%, #000000 100%);
            color: var(--text);
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            overflow: hidden;
            line-height: 1.5;
        }

        .window {
            width: 100%;
            max-width: 550px;
            height: 90vh;
            display: flex;
            flex-direction: column;
            background: var(--window-bg);
            border: 1px solid var(--border);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
            animation: windowAppear 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .title-bar {
            flex-shrink: 0;
            background: var(--titlebar-bg);
            padding: 0.75rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border);
            user-select: none;
        }

        .window-controls {
            display: flex;
            gap: 8px;
        }

        .control {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }
        .close { background: #ff5f56; }
        .minimize { background: #ffbd2e; }
        .maximize { background: #27c93f; }

        .window-title {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-dim);
            letter-spacing: 0.05em;
        }

        .content {
            flex-grow: 1;
            padding: 2.5rem;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: var(--border) transparent;
        }

        h1 {
            font-size: 1.25rem;
            font-weight: 700;
            margin: 0 0 0.5rem 0;
        }

        .subtitle {
            color: var(--text-dim);
            font-size: 0.875rem;
            margin-bottom: 2rem;
        }

        .step-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
            background: rgba(255, 255, 255, 0.03);
            padding: 1rem;
            border-radius: 8px;
        }

        .step-number {
            background: var(--primary);
            color: white;
            min-width: 28px;
            height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
            font-weight: 700;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            font-size: 0.8125rem;
            font-weight: 600;
            margin-bottom: 0.625rem;
            color: var(--text-dim);
        }

        input, select {
            width: 100%;
            padding: 0.75rem 1rem;
            background: #09090b;
            border: 1px solid var(--border);
            border-radius: 8px;
            color: white;
            font-size: 0.875rem;
            box-sizing: border-box;
            transition: all 0.2s;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
        }

        .btn {
            width: 100%;
            padding: 0.875rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.875rem;
            cursor: pointer;
            transition: transform 0.1s, opacity 0.2s;
        }

        .btn:active { transform: scale(0.98); }
        .btn:hover { opacity: 0.9; }

        .status-list {
            list-style: none;
            padding: 0;
            margin-bottom: 2rem;
        }

        .status-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.875rem 0;
            border-bottom: 1px solid var(--border);
            font-size: 0.875rem;
        }

        .status-ok { color: var(--success); }
        .status-error { color: var(--error); }

        .help-box {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.2);
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .help-box strong { color: var(--primary); display: block; margin-bottom: 0.5rem; }
        .help-box code { background: #000; padding: 0.25rem 0.5rem; border-radius: 4px; color: #fff; font-size: 0.75rem; }

        @keyframes windowAppear {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body>
    <div class="window">
        <div class="title-bar">
            <div class="window-controls">
                <div class="control close"></div>
                <div class="control minimize"></div>
                <div class="control maximize"></div>
            </div>
            <div class="window-title">Viewer Installation Wizard</div>
            <div style="width: 50px;"></div> <!-- Spacer -->
        </div>
        
        <div class="content">

        <?php 
        $missingAnyExt = in_array(false, $requirements['extensions'] ?? []);
        if (($missingAnyExt || !$allOk) && $step < 3): 
        ?>
            <div class="help-box">
                <strong>Conseil d'expert :</strong>
                <p style="margin: 0.5rem 0; color: var(--text-dim);">Votre configuration PHP est incomplète.</p>
                <ol style="margin-top: 0.5rem; padding-left: 1.2rem; color: var(--text-dim);">
                    <li>Fichier : <br><code><?= htmlspecialchars((string) $debugInfo['php_ini'], ENT_QUOTES, 'UTF-8') ?></code></li>
                    <li>Activez :<br>
                        <code>extension=pdo_mysql</code><br>
                        <code>extension=pdo_pgsql</code>
                    </li>
                    <li><strong>Redémarrez</strong> votre serveur.</li>
                </ol>
            </div>
        <?php endif; ?>
        
        <?php if ($step == 1): ?>
            <div class="step">
                <div class="step-info">
                    <div class="step-number">1</div>
                    <span style="font-weight: 600;">Vérification système</span>
                </div>
                
                <ul class="status-list">
                    <li class="status-item">
                        <span>Version PHP (<?= PHP_VERSION ?>)</span>
                        <span class="status-icon <?= $requirements['php_version'] ? 'status-ok' : 'status-error' ?>">
                            <?= $requirements['php_version'] ? '✓ OK' : '✗ PHP 8.1+ requis' ?>
                        </span>
                    </li>

                    <?php foreach ($requirements['extensions'] as $ext => $loaded): ?>
                        <li class="status-item">
                            <span>Extension <code><?= $ext ?></code></span>
                            <span class="status-icon <?= $loaded ? 'status-ok' : 'status-error' ?>">
                                <?= $loaded ? '✓ OK' : '✗ Manquante' ?>
                            </span>
                        </li>
                    <?php endforeach; ?>

                    <?php foreach ($requirements['permissions'] as $dir => $isWritable): ?>
                        <li class="status-item">
                            <span>Dossier <code><?= $dir ?></code></span>
                            <span class="status-icon <?= $isWritable ? 'status-ok' : 'status-error' ?>">
                                <?= $isWritable ? '✓ OK' : '✗ Écriture interdite' ?>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <?php if ($allOk): ?>
                    <form method="POST">
                        <?= \Core\Security\Csrf::field() ?>
                        <input type="hidden" name="action" value="step1">
                        <button type="submit" class="btn">Continuer vers la base de données →</button>
                    </form>
                <?php else: ?>
                    <button class="btn" style="background: #334155;" onclick="window.location.reload()">Réessayer</button>
                <?php endif; ?>
            </div>
        <?php elseif ($step == 2): ?>
            <div class="step">
                <div class="step-info">
                    <div class="step-number">2</div>
                    <span style="font-weight: 600;">Base de données</span>
                </div>

                <?php if ($error): ?>
                    <p style="color: var(--error); font-size: 0.8125rem; margin-bottom: 1rem; padding: 0.75rem; background: rgba(239, 68, 68, 0.1); border-radius: 4px;"><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>

                <form method="POST">
                    <?= \Core\Security\Csrf::field() ?>
                    <input type="hidden" name="action" value="step2">
                    <div class="form-group">
                        <label>Moteur</label>
                        <select name="driver">
                            <option value="mysql">MySQL / MariaDB</option>
                            <option value="pgsql">PostgreSQL</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Hôte</label>
                        <input type="text" name="host" value="127.0.0.1" required>
                    </div>
                    <div class="form-group">
                        <label>Nom de la base</label>
                        <input type="text" name="database" value="viewer_system" required>
                    </div>
                    <div class="form-group">
                        <label>Utilisateur</label>
                        <input type="text" name="username" value="root" required>
                    </div>
                    <div class="form-group">
                        <label>Mot de passe</label>
                        <input type="password" name="password">
                    </div>
                    <button type="submit" class="btn">Installer la base →</button>
                </form>
            </div>
        <?php elseif ($step == 3): ?>
            <div class="step">
                <div class="step-info">
                    <div class="step-number">3</div>
                    <span style="font-weight: 600;">Compte Administrateur</span>
                </div>

                <form method="POST">
                    <?= \Core\Security\Csrf::field() ?>
                    <input type="hidden" name="action" value="step3">
                    <div class="form-group">
                        <label>Email Admin</label>
                        <input type="email" name="email" placeholder="admin@example.com" required>
                    </div>
                    <div class="form-group">
                        <label>Mot de passe</label>
                        <input type="password" name="password" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="btn">Finaliser l'installation ✓</button>
                </form>
            </div>
        <?php endif; ?>
    </div> <!-- Close content -->
</div> <!-- Close window -->
</body>
</html>
