#!/usr/bin/env node
const tar = require('tar');
const fs = require('fs');
const path = require('path');
const args = process.argv.slice(2);
const backupFile = args[0];

if (!backupFile) {
    console.log('Usage: sudo node restore.js <backup.tar>');
    console.log('Example: sudo node restore.js /tmp/backup.tar');
    process.exit(1);
}

if (!fs.existsSync(backupFile)) {
    console.error(`Error: File ${backupFile} does not exist`);
    process.exit(1);
}

console.log(`[+] Restoring backup from: ${backupFile}`);
console.log('[+] Extracting to: /root/backups');

const extractPath = '/root/backups';
if (!fs.existsSync(extractPath)) {
    fs.mkdirSync(extractPath, { recursive: true });
}

tar.x({
    file: backupFile,
    cwd: extractPath,
    preservePaths: false
}).then(() => {
    console.log('[+] Restore complete!');
    console.log('[+] Files extracted to:', extractPath);
    
    const files = fs.readdirSync(extractPath);
    console.log('[+] Extracted files:', files.join(', '));
}).catch(err => {
    console.error('[-] Restore failed:', err.message);
    process.exit(1);
});
