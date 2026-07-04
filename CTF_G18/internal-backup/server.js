const fs = require('fs');
const tar = require('tar');
const express = require('express');
const cookieParser = require('cookie-parser');
const fileUpload = require('express-fileupload');
const app = express();

const PORT = 3000;
const ADMIN_USER = 'admin';
const ADMIN_PASS = 'backup_admin_2026';
const EXTRACT_PATH = '/home/tosya/backups';
const TEMP_PATH = '/tmp';

function getTosyaUID() {
    try {
        const passwd = fs.readFileSync('/etc/passwd', 'utf8');
        const line = passwd.split('\n').find(l => l.startsWith('tosya:'));
        if (line) {
            const parts = line.split(':');
            return parseInt(parts[2]);
        }
    } catch(e) {}
    return 1001;
}

const TOSYA_UID = getTosyaUID();
const TOSYA_GID = TOSYA_UID;

console.log(`[+] tosya UID: ${TOSYA_UID}, GID: ${TOSYA_GID}`);

[EXTRACT_PATH, TEMP_PATH].forEach(dir => {
    if (!fs.existsSync(dir)) {
        fs.mkdirSync(dir, { recursive: true });
        fs.chmodSync(dir, 0o755);
        try {
            fs.chownSync(dir, TOSYA_UID, TOSYA_GID);
        } catch(e) {}
    }
});

app.use(cookieParser());
app.use(fileUpload({
    createParentPath: true,
    limits: { fileSize: 50 * 1024 * 1024 },
    useTempFiles: false,
    parseNested: true
}));
app.use(express.urlencoded({ extended: true }));
app.use(express.json());

app.get('/', (req, res) => {
    res.send(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Internal Backup System</title>
            <style>
                body { font-family: monospace; max-width: 600px; margin: 50px auto; padding: 20px; background: #0a0a0a; color: #00ff00; }
                .login-box { background: #1a1a1a; padding: 30px; border-radius: 5px; border: 1px solid #00ff00; }
                input { display: block; width: 100%; padding: 10px; margin: 10px 0; background: #0a0a0a; color: #00ff00; border: 1px solid #333; }
                button { background: #00ff00; color: #0a0a0a; padding: 10px 20px; border: none; cursor: pointer; font-weight: bold; }
                .note { color: #666; font-size: 12px; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class="login-box">
                <h2>🔐 Internal Backup System</h2>
                <p style="color: #888;">Authorized administrators only</p>
                <form method="POST" action="/login">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit">Login</button>
                </form>
            </div>
        </body>
        </html>
    `);
});

app.post('/login', (req, res) => {
    const { username, password } = req.body;
    if (username === ADMIN_USER && password === ADMIN_PASS) {
        res.cookie('admin', 'true', { httpOnly: false });
        res.redirect('/dashboard');
    } else {
        res.send('<h2>❌ Login Failed</h2><p><a href="/">Try again</a></p>');
    }
});

app.get('/dashboard', (req, res) => {
    if (req.cookies?.admin !== 'true') {
        return res.redirect('/');
    }
    res.send(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Backup Dashboard</title>
            <style>
                body { font-family: monospace; max-width: 800px; margin: 50px auto; padding: 20px; background: #0a0a0a; color: #00ff00; }
                .container { background: #1a1a1a; padding: 30px; border-radius: 5px; }
                .upload-form { border: 1px solid #333; padding: 20px; border-radius: 5px; margin: 20px 0; }
                input[type="file"] { background: #0a0a0a; color: #00ff00; padding: 10px; border: 1px solid #333; }
                button { background: #00ff00; color: #0a0a0a; padding: 10px 20px; border: none; cursor: pointer; font-weight: bold; }
                #result { margin-top: 10px; padding: 10px; background: #0a0a0a; border-radius: 3px; }
                .info { color: #888; font-size: 12px; margin-top: 20px; border-top: 1px solid #333; padding-top: 20px; }
                .logout { color: #ff6b6b; text-decoration: none; float: right; }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>📦 Backup Dashboard</h1>
                <p>Welcome, admin! <a href="/logout" class="logout">Logout</a></p>
                <div class="upload-form">
                    <h3>Restore Backup</h3>
                    <p style="color: #ff6b6b;">⚠️ This operation runs with root privileges</p>
                    <form id="uploadForm" enctype="multipart/form-data">
                        <input type="file" name="backup" accept=".tar,.tar.gz,.tgz" required>
                        <button type="submit">Restore</button>
                    </form>
                    <div id="result"></div>
                </div>
            </div>
            <script>
                document.getElementById('uploadForm').addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const formData = new FormData(e.target);
                    const resultDiv = document.getElementById('result');
                    resultDiv.innerHTML = '⏳ Restoring...';
                    try {
                        const response = await fetch('/restore', { method: 'POST', body: formData });
                        const data = await response.json();
                        resultDiv.innerHTML = data.success ? '✅ ' + data.message : '❌ ' + data.error;
                    } catch (error) {
                        resultDiv.innerHTML = '❌ Error: ' + error.message;
                    }
                });
            </script>
        </body>
        </html>
    `);
});

function setPermissionsRecursive(dir, uid, gid, dirMode, fileMode) {
    try {
        if (!fs.existsSync(dir)) return;
        
        fs.chmodSync(dir, dirMode);
        try { fs.chownSync(dir, uid, gid); } catch(e) {}
        
        const items = fs.readdirSync(dir);
        
        for (const item of items) {
            const fullPath = `${dir}/${item}`;
            const stats = fs.lstatSync(fullPath);
            
            if (stats.isDirectory()) {
                setPermissionsRecursive(fullPath, uid, gid, dirMode, fileMode);
            } else {
                try {
                    fs.chmodSync(fullPath, fileMode);
                    try { fs.chownSync(fullPath, uid, gid); } catch(e) {}
                } catch(e) {}
            }
        }
    } catch(e) {
        console.error('Error setting permissions:', e.message);
    }
}

app.post('/restore', async (req, res) => {
    if (req.cookies?.admin !== 'true') {
        return res.status(401).json({ error: 'Unauthorized' });
    }
    
    if (!req.files || !req.files.backup) {
        return res.status(400).json({ error: 'No file uploaded' });
    }
    
    const backupFile = req.files.backup;
    const filePath = `${TEMP_PATH}/upload_${Date.now()}_${backupFile.name}`;
    const extractPath = EXTRACT_PATH;
    
    try {
        await backupFile.mv(filePath);
        console.log(`[+] File saved to: ${filePath}`);
        
        if (!fs.existsSync(extractPath)) {
            fs.mkdirSync(extractPath, { recursive: true });
            fs.chmodSync(extractPath, 0o755);
            try { fs.chownSync(extractPath, TOSYA_UID, TOSYA_GID); } catch(e) {}
        }
        
        await tar.x({
            file: filePath,
            cwd: extractPath,
            preservePaths: false
        });
        
        console.log(`[+] Extracted to: ${extractPath}`);

        setPermissionsRecursive(extractPath, TOSYA_UID, TOSYA_GID, 0o755, 0o644);
        
        console.log(`[+] Permissions fixed: owned by tosya (UID ${TOSYA_UID})`);
        
        try { fs.unlinkSync(filePath); } catch(e) {}
        
        res.json({ 
            success: true, 
            message: `Backup restored successfully to ${extractPath}!`,
            path: extractPath
        });
    } catch (e) {
        console.error('Restore error:', e);
        try { fs.unlinkSync(filePath); } catch(e) {}
        res.status(500).json({ 
            success: false, 
            error: 'Restore failed: ' + e.message 
        });
    }
});

app.get('/logout', (req, res) => {
    res.clearCookie('admin');
    res.redirect('/');
});

app.listen(PORT, '0.0.0.0', () => {
    console.log(`========================================`);
    console.log(`🔐 Internal Backup Service`);
    console.log(`📍 Port: ${PORT}`);
    console.log(`📁 Extract Path: ${EXTRACT_PATH}`);
    console.log(`👤 Owner: tosya (UID: ${TOSYA_UID})`);
    console.log(`========================================`);
});