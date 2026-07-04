
from flask import Flask, request, send_file,session
import sqlite3

app = Flask(__name__)
app.secret_key="ASD1O2I3ASD9J102JDSALKJASDIOJ"

css = """
<style>
    :root { 
        --moon-cyan: #00ffff; 
        --moon-bg: #020205; 
        --alert-red: #ff3333; 
    }
    
    body, html { 
        margin: 0; padding: 0; background-color: var(--moon-bg); 
        color: var(--moon-cyan); font-family: "Courier New", Courier, monospace; 
        height: 100%; overflow: hidden; display: flex; 
        flex-direction: column; align-items: center; justify-content: center; 
    }
    
    /* Scanlines sutis */
    body::after { 
        content: " "; display: block; position: absolute; top: 0; left: 0; 
        bottom: 0; right: 0; z-index: 2; pointer-events: none;
        background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.25) 50%), 
                    linear-gradient(90deg, rgba(255, 0, 0, 0.06), rgba(0, 255, 0, 0.02), rgba(0, 0, 255, 0.06)); 
        background-size: 100% 2px, 3px 100%; 
    }
    
    .panel { 
        z-index: 1; position: relative; padding: 40px; 
        border: 1px solid var(--moon-cyan); 
        box-shadow: 0 0 15px rgba(0, 255, 255, 0.2); 
        background: rgba(0, 255, 255, 0.02); 
        width: 500px; text-align: center;
    }
    
    h1 { 
        border-bottom: 1px dashed var(--moon-cyan); 
        padding-bottom: 10px; font-size: 20px;
        text-shadow: 0 0 8px var(--moon-cyan); 
    }
    
    .ascii { white-space: pre; font-size: 10px; line-height: 10px; margin-bottom: 20px; }
    .error { color: var(--alert-red); text-shadow: 0 0 5px var(--alert-red); font-weight: bold; }
    
    input[type="text"], input[type="password"] { 
        background: transparent; border: none; 
        border-bottom: 1px solid var(--moon-cyan); color: var(--moon-cyan); 
        font-family: inherit; outline: none; width: 80%; margin: 15px 0; 
        text-align: center; padding: 5px;
    }
    
    input[type="submit"], .btn { 
        background: transparent; color: var(--moon-cyan); 
        border: 1px solid var(--moon-cyan); padding: 10px 20px; 
        font-family: inherit; cursor: pointer; margin-top: 20px; 
        text-decoration: none; display: inline-block; 
        transition: 0.3s; 
    }
    
    input[type="submit"]:hover, .btn:hover { 
        background: var(--moon-cyan); color: black; 
    }
</style>
"""


HTML_LOGIN = f"""
<!DOCTYPE html><html><head><title>Moon Base | Admin</title>{css}</head>
<body>
    <div class="panel">
        <div class="ascii">
    __  __  ____   ____  _   _ 
   |  \/  |/ __ \ / __ \| \ | |
   | \  / | |  | | |  | |  \| |
   | |\/| | |  | | |  | | . ` |
   | |  | | |__| | |__| | |\  |
   |_|  |_|\____/ \____/|_| \_|
        </div>
        <h1>[ TERMINAL DE ADMINISTRAÇÃO ]</h1>
        <p>Acesso restrito ao Nível 10.</p>
        <form method="POST">
            <input type="text" name="username" placeholder="Identificação de Usuário" autocomplete="off"><br>
            <input type="password" name="password" placeholder="Código de Autorização"><br>
            <input type="submit" value="INICIAR LINK">
        </form>
    </div>
</body></html>
"""

HTML_SUCCESS = f"""
<!DOCTYPE html><html><head><title>Moon Base | Sincronizado</title>{css}</head>
<body>
    <div class="panel">
        <h1>[ ACESSO CONCEDIDO ]</h1>
        <p>Bem-vindo de volta, Dr. Maxis.</p>
        <p>Status do MPD: <span style="color: yellow;">Pronto para Sincronização.</span></p>
        <p>O artefato foi localizado nos arquivos internos.</p>
        <a href="/download" class="btn">📥 EXTRAIR FOCUSING STONE</a>
    </div>
</body></html>
"""

HTML_DENIED = f"""
<!DOCTYPE html><html><head><title>Erro de Autenticação</title>{css}</head>
<body>
    <div class="panel">
        <h1 class="error">[ ALERTA DE INTRUSÃO ]</h1>
        <p class="error">Credenciais inválidas. O incidente foi reportado.</p>
        <a href="/" class="btn" style="border-color: var(--alert-red); color: var(--alert-red);">TENTAR NOVAMENTE</a>
    </div>
</body></html>
"""

HTML_403 = f"""
<!DOCTYPE html><html><head><title>Acesso Negado</title>{css}</head>
<body>
    <div class="panel">
        <h1 class="error">[ ERRO 403 - ACESSO NEGADO ]</h1>
        <p>Apenas o Administrador Chefe (Maxis) possui autorização de nível Omega para interagir com os artefatos.</p>
    </div>
</body></html>
"""



def init_db():
    conn = sqlite3.connect('moon.db')
    c = conn.cursor()
    c.execute('CREATE TABLE IF NOT EXISTS users (username TEXT, password TEXT)')
    # O usuario legitimo seria o maxis, mas a senha é impossível de quebrar na força bruta
    c.execute("INSERT INTO users (username, password) VALUES ('maxis', 'senha_super_secreta_dr_maxis_115')")
    conn.commit()
    conn.close()

@app.route('/', methods=['GET', 'POST'])
def login():
    if request.method == 'POST':
        user = request.form.get('username')
        password = request.form.get('password')

        conn = sqlite3.connect('moon.db')
        c = conn.cursor()
        
        # permite SQLi  
        query = f"SELECT * FROM users WHERE username = '{user}' AND password = '{password}'"
        
        try:
            c.execute(query)
            result = c.fetchone()
        except:
            result = None

        if result:
            session['logged_in'] = True
            session['user'] = result[0]

            return HTML_SUCCESS

        else:
            return HTML_DENIED
    return HTML_LOGIN

@app.route('/download')
def download():

    if not session.get('logged_in') or session.get('user') != 'maxis':
        return HTML_403, 403

    return send_file('focusing_stone.jpg', as_attachment=True)

if __name__ == '__main__':
    init_db()
    # Roda apenas internamente na porta 8080
    app.run(host='127.0.0.1', port=8080)
