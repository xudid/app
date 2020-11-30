function update(url, elementId) {
    $.post(url, function(response){
       let obj = JSON.parse(response);
        let element = $('#' + elementId);
        element.html(obj.quantity);
    }.bind(elementId))
        .fail(function() {
            console.log('fail');
        })
}
class Input
{
    static change(url, element) {
        $.post(url, function(reponse) {
            let obj = JSON.parse(reponse);
            element.html(obj.quantity)
        }.bind(element))
            .fail(function() {
                console.log('fail');
            })
    }
    static input(url, element) {
        console.log(url);
        console.log(element.value)
    }
}

class Button
{
    static post(url, data, callback)
    {
        $.post(url, data, callback);
    }
}

