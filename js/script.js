function deleteData(url, selector, id)
{
    $.ajax({
        url: base_url+"/"+url,
        type: "POST",
        dataType: "json",
        // параметры запроса, передаваемые на сервер (последний - подстрока для поиска):
        data: {id: id},
        // обработка успешного выполнения запроса
        success: function(data){

        },
        error: function(){
            alert('error');
            //TODO показать ошибку транслате
        }
    });
}


$(function() {
    /**
     * Автокомплит прикрепления юзера к проекту
     * Autocomplete user attachment to the project
     */
    $( "#userAutocomplete" ).autocomplete({
        source: function(request, response){
            $.ajax({
                url: base_url+"/task/getName",
                type: "POST",
                dataType: "json",
                // параметры запроса, передаваемые на сервер (последний - подстрока для поиска):
                data:{
                    maxRows: 12,
                    nameUser: request.term
                },
                // обработка успешного выполнения запроса
                success: function(data){
                    // приведем полученные данные к необходимому формату и передадим в предоставленную функцию response
                    response($.map(data.users, function(item){
                        var error = '';

                        switch(item.name)
                        {
                            case "notAjax_EX":
                            case "notPostData_EX":
                                error = jsLang[0];
                                break;
                            case "notMatch_EX":
                                error = jsLang[1];
                                break;
                        }

                        if(error != '')
                            $("#autocomplete_error").html(error);
                        else
                        {
                            $("#autocomplete_error").html('');
                            return{
                                label: item.name+item.login,
                                value: item.login
                            }
                        }

                    }));
                }
            });
        },
        minLength: 3, //срабатывание при минимальном количестве символов
        delay: 200, //задержка между запросами
        autoFocus: true
        /*select: function (e, ui)
        {
            $("#userAutocompleteHide").val(ui.item.value);
        }*/
    });

    //скрывает автокоплит, если выбран чекбокс (It hides 'input autocomplete' if the checkbox is selected)
    $("#iAdmin").click(function(){
        if(document.getElementById('iAdmin').checked)
            $("#userAutocomplete").prop("disabled", true);
        else
            $("#userAutocomplete").prop("disabled", false);
    });


});