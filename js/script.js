/**
 * Удаляем любые данные
 * Remove any data
 *
 * @param url - на какую страницу делаем запрос (on what page we do request)
 * @param selector - как называется селектор той строки, которую впоследствии из вида удалим (the name of the selector line, which was later remove from view)
 * @param id - ид того, что удаляем (id that remove)
 * @param btn_delete_selector - селектор кнопки удаления, чтобы ее можно было скрыть (selector delete button, so that it can be hidden)
 */
function deleteData(url, selector, id, btn_delete_selector)
{
    $.ajax({
        url: base_url+"/"+url,
        type: "POST",
        dataType: "json",
        // параметры запроса, передаваемые на сервер (последний - подстрока для поиска):
        data: {id: id},
        // обработка успешного выполнения запроса
        beforeSend: function(){
            showLoad(btn_delete_selector, id, false);
        },
        success: function(data){
            if(data.status == 'error')
            {
                $("#mainError").removeClass("label-success").addClass("label-danger").html(data.result);
                showLoad(btn_delete_selector, id, true);
            }
            else
            {
                $("#"+selector+id).fadeOut(300, function(){
                    $("#mainError").removeClass("label-danger").addClass("label-success").html(data.result);
                    $(this).remove();
                    if($(".project").html() === undefined)
                        $("#addProject").remove();

                });
            }
        },
        error: function(){
            $("#mainError").removeClass("label-success").addClass("label-danger").html(jsLang[2]);
            showLoad(btn_delete_selector, id, true);
        }
    });
}


function showLoad(selector, id, back)
{
    if(back === false)
    {
        $("#" + selector + id).hide();
        $("#load_" + id).show();
    }
    else
    {
        $("#load_" + id).fadeOut(300, function(){
            $("#" + selector + id).show();
        });
    }
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

                    if(data.status == 'error')
                    {
                        $("#autocomplete_error").html(data.result);
                        return false;
                    }

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