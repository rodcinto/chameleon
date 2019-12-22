var $ = require('jquery');

class Message {
    constructor() {
        this.displayDuration = 2;
        this.messageID = null;
    }

    setMessageID() {
        this.messageID = 'alert-' + Date.now();
    }

    createMessageDOM(message, type = 'primary') {
        const markup = document.createElement('div');
        markup.className = 'alert ' + 'alert-' + type;
        markup.setAttribute('role', 'alert');
        markup.setAttribute('id', this.messageID);
        markup.innerText = message;
        return markup;
    }

    appendMessage(messageDOM, target) {
        target.append(messageDOM);
    }

    waitAndDeleteMessage() {
        var tempMessageID = '#' + this.messageID;
        setTimeout(() => {
            $(tempMessageID).fadeTo(900, 0).slideUp(900, () => {
                $(this).remove();
            });
        }, this.displayDuration * 1000);
    }

    flash(message, type) {
        this.setMessageID();
        const messageDOM = this.createMessageDOM(message, type);
        this.appendMessage(messageDOM, $('#messages'));
        this.waitAndDeleteMessage();
    }
}

module.exports = Message;
