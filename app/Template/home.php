<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dobby Chat</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #e8ecf1;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }

        .chat-wrapper {
            width: 100%;
            max-width: 700px;
            margin-top: 40px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        header {
            background: #2c3e50;
            color: white;
            padding: 1rem;
            text-align: center;
        }

        header h1 {
            margin: 0;
            font-size: 1.4rem;
        }

        #chat {
            flex: 1;
            padding: 1rem;
            overflow-y: auto;
            height: 500px;
            display: flex;
            flex-direction: column;
        }

        .message {
            margin: 0.4rem 0;
            padding: 0.75rem 1rem;
            border-radius: 20px;
            max-width: 80%;
            word-wrap: break-word;
            font-size: 0.95rem;
            line-height: 1.4;
        }

        .user {
            background: #3498db;
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 5px;
        }

        .ai {
            background: #f1f0f0;
            color: #333;
            align-self: flex-start;
            border-bottom-left-radius: 5px;
        }

        #input-form {
            display: flex;
            padding: 0.75rem 1rem;
            border-top: 1px solid #ccc;
            background: #fafafa;
        }

        #promt {
            flex: 1;
            padding: 0.6rem 0.75rem;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            padding: 0.6rem 1rem;
            font-size: 1rem;
            margin-left: 0.5rem;
            background: #2c3e50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:disabled {
            background: #95a5a6;
            cursor: not-allowed;
        }

        .dobby {
            max-width: 50px;
            margin: 0 auto;
        }
    </style>
</head>

<body>
    <div class="chat-wrapper">
        <header>
            <img src="dobby.png" class="dobby" alt="dobby">
            <h1>Talk to Dobby</h1>
        </header>

        <div id="chat"></div>

        <form id="input-form">
            <input type="text" id="promt" placeholder="Ask Dobby something..." required autocomplete="off" />
            <button type="submit">Send</button>
        </form>
    </div>

    <script>
        const chatContainer = document.getElementById('chat');
        const form = document.getElementById('input-form');
        const promtInput = document.getElementById('promt');
        const button = form.querySelector('button');

        let chatHistory = JSON.parse(localStorage.getItem('chatHistory') || '[]');

        function renderMessages() {
            chatContainer.innerHTML = '';
            chatHistory.forEach(msg => {
                const div = document.createElement('div');
                div.className = 'message ' + (msg.from === 'user' ? 'user' : 'ai');
                div.textContent = msg.text;
                chatContainer.appendChild(div);
            });
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        async function sendpromt(promt) {
            const userMessage = {
                from: 'user',
                text: promt
            };
            chatHistory.push(userMessage);
            renderMessages();

            button.disabled = true;

            try {
                const res = await fetch('/api', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        promt
                    })
                });

                const data = await res.json();
                const aiMessage = {
                    from: 'ai',
                    text: data.answer || 'No answer received.'
                };
                chatHistory.push(aiMessage);
            } catch (e) {
                chatHistory.push({
                    from: 'ai',
                    text: '⚠️ Error contacting Dobby.'
                });
            }

            localStorage.setItem('chatHistory', JSON.stringify(chatHistory));
            renderMessages();
            button.disabled = false;
        }

        form.addEventListener('submit', e => {
            e.preventDefault();
            const promt = promtInput.value.trim();
            if (promt !== '') {
                sendpromt(promt);
                promtInput.value = '';
                promtInput.focus();
            }
        });

        renderMessages();
        promtInput.focus();
    </script>
</body>

</html>