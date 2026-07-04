const express = require('express');
const app = express();
const users = {
    'asya': 'fcddbc1ec349b6b8b55fd0ab0a1e369b',
    'tosya': '83da15ce07eae8632b0490f42c010664',
    'test_user': '482c811da5d5b4bc6d497ffa98491e38',
    'developer': 'f2c973c7b46aa649ceeddc2ff40bf390',
    'support': '37fd02980ada84932f783adcd6c1f860',
    'qa': '4691e1c8daf70fc99c119fef5086ef38',
    'deploy': 'cbdb4e7fc0a97e97beb76122beb8336c'
};

app.get('/', (req, res) => {
    res.send(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>TechSlop - User Management Panel</title>
            <style>
                body { font-family: 'Segoe UI', Tahoma, sans-serif; max-width: 900px; margin: 30px auto; padding: 20px; background: #f0f2f5; color: #333; }
                h1 { color: #1a2a6c; border-bottom: 2px solid #dee2e6; padding-bottom: 10px; }
                .warning { background: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107; margin: 20px 0; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; background: white; border-radius: 8px; overflow: hidden; }
                th { background: #1a2a6c; color: white; padding: 12px; text-align: left; }
                td { padding: 12px; border-bottom: 1px solid #dee2e6; }
                tr:hover { background: #f8f9fa; }
                .highlight { background: #d1e7dd; }
                .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: bold; }
                .badge-admin { background: #dc3545; color: white; }
                .badge-backup { background: #fd7e14; color: white; }
                .badge-user { background: #28a745; color: white; }
                .badge-dev { background: #007bff; color: white; }
                .badge-ssh-enabled { background: #28a745; color: white; padding: 2px 8px; border-radius: 10px; font-size: 10px; }
                .badge-ssh-disabled { background: #dc3545; color: white; padding: 2px 8px; border-radius: 10px; font-size: 10px; }
                .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #dee2e6; color: #6c757d; font-size: 12px; }
            </style>
        </head>
        <body>
            <h1>User Management Panel</h1>
            <div class="warning">
                ⚠️ Internal User Database - Authorized Personnel Only
            </div>

            <h2>Registered Users</h2>
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Role</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="highlight">
                        <td><strong>asya</strong></td>
                        <td><code>fcddbc1ec349b6b8b55fd0ab0a1e369b</code></td>
                        <td><span class="badge badge-admin">Developer</span></td>
                        <td><span class="badge badge-ssh-enabled">Enabled</span></td>
                    </tr>
                    <tr class="highlight">
                        <td><strong>tosya</strong></td>
                        <td><code>83da15ce07eae8632b0490f42c010664</code></td>
                        <td><span class="badge badge-backup">Backup Admin</span></td>
                        <td><span class="badge badge-ssh-enabled">Enabled</span></td>
                    </tr>
                    <tr>
                        <td>test_user</td>
                        <td><code>482c811da5d5b4bc6d497ffa98491e38</code></td>
                        <td><span class="badge badge-admin">Administrator</span></td>
                        <td><span class="badge badge-ssh-disabled">Disabled</span></td>
                    </tr>
                    <tr>
                        <td>developer</td>
                        <td><code>f2c973c7b46aa649ceeddc2ff40bf390</code></td>
                        <td><span class="badge badge-dev">Developer</span></td>
                        <td><span class="badge badge-ssh-disabled">Disabled</span></td>
                    </tr>
                    <tr>
                        <td>support</td>
                        <td><code>37fd02980ada84932f783adcd6c1f860</code></td>
                        <td><span class="badge badge-user">Support</span></td>
                        <td><span class="badge badge-ssh-disabled">Disabled</span></td>
                    </tr>
                    <tr>
                        <td>qa</td>
                        <td><code>4691e1c8daf70fc99c119fef5086ef38</code></td>
                        <td><span class="badge badge-user">QA</span></td>
                        <td><span class="badge badge-ssh-disabled">Disabled</span></td>
                    </tr>
                    <tr>
                        <td>deploy</td>
                        <td><code>cbdb4e7fc0a97e97beb76122beb8336c</code></td>
                        <td><span class="badge badge-dev">Deployment</span></td>
                        <td><span class="badge badge-ssh-disabled">Disabled</span></td>
                    </tr>
                </tbody>
            </table>

            <div class="footer">
                TechSlop Panel | Internal Use Only
            </div>
        </body>
        </html>
    `);
});

app.listen(80, '0.0.0.0', () => {
    console.log('Panel running on port 80');
});