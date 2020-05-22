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
        const _sendForm = document.getElementById('chat-form');

        // Fonction d'envoie du message
        const sendMessage = (message) => {
            if (message === '') {
                return;
            }

            fetch(_sendForm.action, {
                method: _sendForm.method,
                body: 'message=' + message,
                headers: new Headers({
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                })
            }).then(() => {
                _messageInput.value = '';
            });
        };

        // Evenement sur le form
        _sendForm.onsubmit = (evt) => {
            sendMessage(_messageInput.value);

            evt.preventDefault();
            return false;
        };

        // Abonnement Mercure
        const url = new URL(chat.mercurePublishUrl);
        url.searchParams.append('topic', chat.ApiBaseUrl + '/message');

        const eventSource = new EventSource(url, { withCredentials: true });
        eventSource.onmessage = (evt) => {
            const data = JSON.parse(evt.data);
            console.log(data);
            if (!data.message) {
                return;
            }
            _receiver.insertAdjacentHTML('beforeend', `<div class="message--sended">${data.message}</div>`);
            };
        }
};

document.addEventListener('DOMContentLoaded', chat.init);