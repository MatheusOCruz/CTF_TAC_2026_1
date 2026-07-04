const express = require('express');
const path = require('path');
const app = express();

app.use(express.static('public'));
app.get('*', (req, res) => {
    res.sendFile(path.join(__dirname, 'public', 'index.html'));
});
app.listen(80, '0.0.0.0', () => {
    console.log('TechSlop portal running on port 80');
});