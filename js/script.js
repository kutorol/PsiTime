$(function() {
    /**
     * Автокомплит прикрепления юзера к проекту
     * TODO заменить язык на разный и доделать автозаполнение с тегами в инпуте
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
                                error = 'Вы пытаетесь отправить неправильный запрос!';
                                break;
                            case "notMatch_EX":
                                error = "Такого пользователя нет!";
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
        autoFocus: true,
        select: function (e, ui) {
            //TODO сделать добавление логина в hidden поле с ид userAutocompleteHide
            alert(ui.item.value); //тут логин чувака
            console.log(ui);
        }
    });



});