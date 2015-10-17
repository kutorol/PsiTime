function delAttach(src, id)
{
    //шаблон ajax запроса
    ajaxRequestJSON("task/delAttach");
    $.ajax({
        data: {src: src},
        success: function(data)
        {
            console.log(data);
            if(data.status == 'error')
            {
                errorSuccessAjax({title: data.resultTitle, message: data.resultText}); //показываем модалку
                return false;
            }

            hideLoad();
            //удаляем прикрепленный файл
            var deleteAttach = $("#"+id);
            deleteAttach.fadeOut(150, function(){
                deleteAttach.remove();
            });
        }
    });
}

function uploadAttachFile()
{
    //не удалять! если удалить какой нибудь прикрепленный файл, то настройки для ajax сохраняться и прелоадер будет вечно показываться!
    //do not delete! if you remove some sort of attachment, the setup, for ajax preloader will persist forever show!
    $.ajaxSetup({
        beforeSend: function(){}
    });

    //главный id, куда будут вставляться прикрепленные файлы
    var attachFile = $('#fileAttach');
    //прогресс бар
    var progressBar = $('#bar');
    $('#fileupload').fileupload({
        type: "POST",
        dataType: "json",
        error: function(e, x, settings, exception)
        {
            $('#bar').css('width','0%');
            var statusErrorMap = {
                '400' : jsLang[8],
                '401' : jsLang[9],
                '403' : jsLang[10],
                '404' : jsLang[11],
                '500' : jsLang[12],
                '503' : jsLang[13]
            };

            if (e.status)
            {
                message = statusErrorMap[e.status];
                if(!message)
                    message = jsLangAdditional;
            }
            else if(exception == 'parsererror')
                message = jsLang[14];
            else if(exception == 'timeout')
                message = jsLang[15];
            else if(exception == 'abort')
                message = jsLang[16];
            else
                message = jsLangAdditional;

            //показываем ошибку
            addTitle({title: jsLang[7], message: message});
        },
        progressall: function (e, data)
        {
            var progress = parseInt(data.loaded / data.total * 100, 10); //расчет в процентах статус загрузки
            progressBar.css('width', progress + '%');  // вывод в статус бар
        },
        done: function (e, data)
        {
            hideLoad();
            progressBar.css('width','0%');
            response = data.result;

            addTitle({title: response.resultTitle, message: response.resultText});
            if(response.status == 'error')
                return false;

            //FIXME text
            var textAppend = '<div class="col-lg-2" id="'+response.id+'"><div title="Удалить" onclick="delAttach(\''+response.fileSrc+'\', \''+response.id+'\');" class="btn btn-danger deleteAttachFile"><i class="fa fa-times"></i></div><div class="thumbnail" align="center">';
            textAppend += '<div class="options" data-ext="'+response.extension+'" onClick="showDownloadImageDoc(\''+response.fileSrc+'\', \''+response.extension+'\', \''+response.titleFile+'\');" title="'+response.titleFile+'">';

            switch(response.extension)
            {
                case 'pdf':
                    textAppend += '<i class="fa fa-file-pdf-o iconPdf"></i></div>';
                    break;
                case "word":
                    textAppend += '<i class="fa fa-file-word-o iconWord"></i></div>';
                    break;
                case "exel":
                    textAppend += '<i class="fa fa-file-excel-o iconExel"></i></div>';
                    break;
                case "pPoint":
                    textAppend += '<i class="fa fa-file-powerpoint-o iconPPoint"></i></div>';
                    break;
                case "text":
                    textAppend += '<i class="fa fa-file-text-o iconAttach"></i></div>';
                    break;
                case "video":
                    textAppend += '<i class="fa fa-file-video-o iconAttach"></i></div>';
                    break;
                case "audio":
                    textAppend += '<i class="fa fa-file-audio-o iconAttach"></i></div>';
                    break;
                case "img":
                    textAppend += '<img src="'+response.fileSrc+'" alt="'+response.titleFile+'"></div>';
                    break;
                default:
                    textAppend += '<i class="fa fa-file-archive-o iconAttach"></i></div>';
            }

            textAppend += '<div class="longText" title="'+response.titleFile+'" >'+response.titleFile+'</div></div></div>';

            attachFile.append(textAppend);
        }
    });
}

$(document).ready(function(){

    /**
     * Когда перетаскиваем файл в специальное поле
     * When drag and drop files into a special field
     */
    $("#dropZone").bind('drop dragover', function (e) { // указал дроп зону
        e.preventDefault();
        uploadAttachFile();
    });

    /**
     * При нажатии на фейковую кнопку "Выбрать файл", открываем настоящую кнопку
     * By clicking on phishing web button "Choose File" button to open this
     */
    $("#fake_upload_button").click(function(){ // по нажатию, нажимаем окно добавления файла
        $('#fileupload input').click();
    });
    //эта функция сработает тогда, когда нажмем на фейковую кнопку "Выбрать файл"
    uploadAttachFile();
});