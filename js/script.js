$(function() {
    /**
     * Автокомплит прикрепления юзера к проекту
     */
    $( "#userAutocomplete" ).autocomplete({
        source: function(request, response){
            $.ajax({
                url: base_url+"/task/getName",
                dataType: "json",
                // параметры запроса, передаваемые на сервер (последний - подстрока для поиска):
                data:{
                    maxRows: 12,
                    nameUser: request.term
                },
                // обработка успешного выполнения запроса
                success: function(data){
                    // приведем полученные данные к необходимому формату и передадим в предоставленную функцию response
                    response($.map(data.response, function(item){
                        console.log(data);


                            return{
                                label: item.name,
                                value: item.name
                            }


                    }));
                }
            });
        },
        minLength: 3, //срабатывание при минимальном количестве символов
        delay: 200 //задержка между запросами
    });

});