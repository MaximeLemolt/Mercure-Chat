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
        const _conversation = document.getElementById('messages');
        const _sendForm = document.getElementById('chat-form');
        const _userId = document.getElementById('conversation').dataset.user;
        // Scroll position bottom if messages out of the box
        _conversation.scrollTop = _conversation.scrollHeight;

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
        // Subscribe to message the user send or received
        url.searchParams.append('topic', chat.ApiBaseUrl + '/message/{id}/' + _userId);
        url.searchParams.append('topic', chat.ApiBaseUrl + '/message/' + _userId + '/{id}');

        const eventSource = new EventSource(url, { withCredentials: true });
        eventSource.onmessage = (evt) => {
            const data = JSON.parse(evt.data);
                // console.log(data);
                if (!data.message) {
                    return;
                }

                // Check if the message is about the current conversation
                if (data.message.author == _conversation.dataset.recipient || data.message.author == _userId)
                {
                    if (document.getElementById('no-messages') != null) {
                        document.getElementById('no-messages').remove();
                    }
    
                    if (data.message.author == _conversation.dataset.recipient) {
                        _receiver.insertAdjacentHTML('beforeend',
                        `<div class="message--received">
                            <p class="message-content m-0">${data.message.content}</p>
                            <p class="message-date m-0">${data.message.date}</p>
                        </div>`
                        );
                    } else {
                        _receiver.insertAdjacentHTML('beforeend',
                        `<div class="message--sended">
                            <p class="message-content m-0">${data.message.content}</p>
                            <p class="message-date m-0">${data.message.date}</p>
                        </div>`
                        );
                    }
                    // Scroll position bottom if messages out of the box
                    _conversation.scrollTop = _conversation.scrollHeight;
                } else {
                    const usersList = document.getElementById('users-list').getElementsByTagName('a');
                    console.log(usersList);

                    for (let user of usersList) {
                        if (user.dataset.userId == data.message.author) {
                            user.innerHTML += '<span class="badge badge-danger badge-pill">1</span>';
                        }
                    }
                }
            };
        }
};

document.addEventListener('DOMContentLoaded', chat.init);