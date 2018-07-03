
function handler() {
    if (document.getElementById('answer-4').checked) {
        document.getElementById('answer-other').style.display = 'block';
        document.getElementById('unsubscribe-count').style.display = 'block';
    }
    else {
        document.getElementById('answer-other').style.display = 'none';
        document.getElementById('unsubscribe-count').style.display = 'none';
    }
}

$(document).ready(function() {
    var ex1 = document.getElementById('answer-1');
    ex1.onclick = handler;

    var ex2 = document.getElementById('answer-2');
    ex2.onclick = handler;

    var ex3 = document.getElementById('answer-3');
    ex3.onclick = handler;

    var ex4 = document.getElementById('answer-4');
    ex4.onclick = handler;

    document.getElementById('answer-other').onkeyup = function() {
        if (this.value.length <= 40) {
            document.getElementById('unsubscribe-count').style.color = '#0A8ED0';
            document.getElementById('unsubscribe-count').innerHTML = "Characters left: " + (45 - this.value.length);
            document.getElementById('submit').disabled = false;
        } else {
            document.getElementById('unsubscribe-count').style.color = 'red';
            document.getElementById('unsubscribe-count').innerHTML = "Too long";
            document.getElementById('submit').disabled = true;
        }
    };

});