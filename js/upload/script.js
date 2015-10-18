/**
 * Удаляем прикрепленные файлы к добавляемой задачи
 * Remove attachments to the task
 * @param src - полный путь к файлу (the full path to the file)
 * @param id - это DOM элемент, который уберем из виду, показав что файл удален (This DOM element that will clean out of sight, showing that the file is deleted)
 */
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
            deleteAttach.fadeOut(300, function(){
                deleteAttach.remove();
            });
        }
    });
}

/**
 * Если переносим или добавляем файл через кнопку, то эта функция добавит файл к добавляемой задачи, и отобразит его в нужном нам формате (zip, img, video, audio etc.)
 * If you move or add files via the button, this function will add a file to add tasks, and display it in the required format (zip, img, video, audio etc.)
 */
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
            var textAppend = '<div class="col-lg-2" id="'+response.id+'"><div title="'+jsLang[21]+'" onclick="delAttach(\''+response.fileSrc+'\', \''+response.id+'\');" class="btn btn-danger deleteAttachFile"><i class="fa fa-times"></i></div><div class="thumbnail" align="center">';
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

/**
 * При нажатии на иконку картинки, в модальном окне показывается увеличенная картинка. Если это другой файл, то либо его показываем, либо скачиваем
 * Clicking on the icon image in a modal window shows an enlarged picture. If it's another file, or a show, or download the
 */
function showDownloadImageDoc(src, ext, title)
{
    //если картинка, то показываем ее в модалке
    if(ext == 'img')
    {
        var img = '<img src="' + src + '" class="img-responsive"/>';
        showModal(title, img);
    }
    //если видео, аудио - показываем в модальном окне и делаем кнопку скачать
    else
    {
        var srcFile = src.split('/');
        srcFile = srcFile[srcFile.length-1];
        var windowParam = 'width=500,height=600,resizable=yes,scrollbars=yes,status=yes';
        var secondContetn = '<br><br><div onclick="window.open(\''+base_url+'/task/download/'+srcFile+'\', \''+jsLang[23]+' '+title+'\', \''+windowParam+'\');" class="btn btn-warning">'+jsLang[23]+' <i class="fa fa-download"></i></div>';

        if(ext == 'audio')
        {
            var audio = '<audio controls><source src="'+src+'" preload="metadata">'+jsLang[22]+'</audio>';
            showModal(title, audio+secondContetn);
        }
        else if(ext == 'video')
        {
            var srcVideo = '<script src="'+base_url_start+'js/jwplatform.js"></script><div id="myElement">'+jsLang[24]+'</div><script type="text/javascript">var playerInstance = jwplayer("myElement");'
                +'playerInstance.setup({file: "'+src+'", width: 640, height: 360, title: "'+title+'"});</script>';
            showModal(title, srcVideo+secondContetn);
        }
        //если прочие файлы, то сразу даем их скачать
        else
            window.open(src, title, windowParam);
    }


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
    //This function works when fake click on the "Choose File"
    uploadAttachFile();
});