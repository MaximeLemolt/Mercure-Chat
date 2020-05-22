/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.scss';

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
// import $ from 'jquery';

let chat = {

    ApiBaseUrl: 'http://localhost:8000',
    mercurePublishUrl: 'http://localhost:3000/.well-known/mercure',

    init: function() {
        const _receiver = document.getElementById('messages');
        const _messageInput = document.getElementById('message-input');
        const _conversation = document.getElementById('conversation');
        const _sendForm = document.getElementById('chat-form');

        // Fonction d'envoie du message
        const sendMessage = (message, recipient) => {
            if (message === '') {
                return;
            }

            fetch(_sendForm.action, {
                method: _sendForm.method,
                body: JSON.stringify({
                    content: message,
                    recipient: recipient
                }),
                headers: new Headers({
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                })
            }).then(() => {
                _messageInput.value = '';
            });
        };

        // Evenement sur le form
        _sendForm.onsubmit = (evt) => {
            sendMessage(_messageInput.value, _conversation.dataset.recipient);

            evt.preventDefault();
            return false;
        };

        // Abonnement Mercure
        const url = new URL(chat.mercurePublishUrl);
        url.searchParams.append('topic', chat.ApiBaseUrl + '/message');

        const eventSource = new EventSource(url, { withCredentials: true });
        eventSource.onmessage = (evt) => {
            const data = JSON.parse(evt.data);

            if (!data.message) {
                return;
            }

            if (document.getElementById('no-messages') != null) {
                document.getElementById('no-messages').remove();
            }

            if (data.author == _conversation.dataset.recipient) {
                _receiver.insertAdjacentHTML('beforeend', `<div class="message--received">${data.message}</div>`);
            } else {
                _receiver.insertAdjacentHTML('beforeend', `<div class="message--sended">${data.message}</div>`);
            }
            };
        }
};

document.addEventListener('DOMContentLoaded', chat.init);