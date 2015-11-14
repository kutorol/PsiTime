<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Lang_controller
{
    /**
     * Возвращаем язык для js (ошибки, уведомления и прочее)
     * Return language js (bugs, notifications, etc.)
     * @param string $segment
     * @return mixed
     */
	public function getLang($segment = 'ru')
	{
        /**
         * Общее для всех языков
         * The total for all languages
         */
        $lang['languages_desc'] = [
            0   =>  [
                        'ru'    =>  'Язык сайта',
                        'en'    =>  'Site language',
                        //add next here
                        //'de'    =>  'Website-Sprache wählen',

                        //DON'T TOUCH!!!
                        'perPageLang'   =>  [
                                                'ru'    =>  ['first_link'   =>  'Первая', 'last_link' =>  'Последняя', 'prev_link'=>"Предыдущая", "next_link" => "Следующая"],
                                                'en'    =>  ['first_link'   =>  'First', 'last_link' =>  'Last', 'prev_link'=>"Previous", "next_link" => "Next"],
                                                //'de'  =>  ['first_link'   =>  'Erste', 'last_link' =>  'Letzte', 'prev_link'=>"Zurück", "next_link" => "Der nächste"],
                                            ],
                        'errorAuth'     =>  [
                                                'ru'    =>  'Вам необходимо авторизоваться!',
                                                'en'    =>  'Please log in!'
                                            ],
                        'titleError'    =>  [
                                                'ru'    =>  'Ошибка!',
                                                'en'    =>  'Error!'
                                            ],
                        'titleErrorMessage'    =>  [
                                                'ru'    =>  "Попробуйте обновить страницу, если не поможет - сообщите об ошибке на <a href=''>этой страницу</a>",
                                                'en'    =>  "Try to refresh the page if you do not help - report an error <a href=''>on this page</a>"
                                            ],
                    ],
            'ru'   =>   'Русский',
            'en'   =>   'English',
            //add next here
            //'de'  =>  'Deutsch',
        ];

        /**
         * Перевод для полей форм
         * Translations for form fields
         */
        $lang['input_form_lang'] = [
            0   =>  [
                        'ru'    =>  'Логин',
                        'en'    =>  'Login',
                    ],
            1   =>  [
                        'ru'    =>  'Имя',
                        'en'    =>  'First Name',
                    ],
            2   =>  [
                        'ru'    =>  'Пароль',
                        'en'    =>  'Password',
                    ],
            3   =>  [
                        'ru'    =>  'Повторите пароль',
                        'en'    =>  'Repeat password',
                    ],
            4   =>  [
                        'ru'    =>  'Комментарий',
                        'en'    =>  'Сomment',
                    ],
            5   =>  [
                        'ru'    =>  'Войти',
                        'en'    =>  'Login',
                    ],
            6   =>  [
                        'ru'    =>  'Регистрация',
                        'en'    =>  'Register',
                    ],
            7   =>  [
                        'ru'    =>  'Забыли пароль?',
                        'en'    =>  'Forgot your password?',
                    ],
            8   =>  [
                        'ru'    =>  'Восстановить',
                        'en'    =>  'Reestablish',
                    ],
            9   =>  [
                        'ru'    =>  'Изменить пароль',
                        'en'    =>  'Change password'
                    ],
            10  =>  [
                        'ru'    =>  'Старый пароль',
                        'en'    =>  'Old password'
                    ],
            11  =>  [
                        'ru'    =>  'Новый пароль',
                        'en'    =>  'New password'
                    ],
            12  =>  [
                        'ru'    =>  'Повторите новый пароль',
                        'en'    =>  'Repeat new password'
                    ],
            13  =>  [
                        'ru'    =>  'Редактировать профиль',
                        'en'    =>  'Edit profile'
                    ],
        ];

        /**
         * Перевод для верхнего меню
         * Translation for top menu
         */
        $lang['header_menu_lang'] = [
            0   =>  [
                        'ru'    =>  'На Главную',
                        'en'    =>  'To main'
                    ],
            1   =>  [
                        'ru'    =>  'Выйти',
                        'en'    =>  'Logout'
                    ],
        ];

        $additional_ru = "Класс: :_class_:; метод: :_method_:; строка: :_line_:.";
        $lang['lang_ru']['error_code'] = [
            1   =>  ["Вам нужно было передавать параметр в функцию, который равен id html элемента, отвечающий за то, какой параметр задачи обновляем! ".$additional_ru, " Возможно вы изменили дизайн, и некоторые html элементы были удалены, которые должны были присутствовать."],
            2   =>  "Вы не передали данные об задаче в эту функцию! ".$additional_ru,
            3   =>  [0=>"Задачи ищутся с применением фильтра, но вот данные для фильтра не валидны. ", 1=> $additional_ru, 2 => "На данный момент показаны все задачи без фильтра."],

        ];

        $additional_en = "Class: :_class_:; method: :_method_:; line: :_line_:.";
        $lang['lang_en']['error_code'] = [
            1   =>  ["You had to pass a parameter to a function, which is id html element responsible for setting a task update! ".$additional_en, " Maybe you changed the design, and some html elements have been removed, which should have been present."],
            2   =>  "You do not pass the data about the task in this function! ".$additional_en,
            3   =>  [0=>"Tasks are looked for with use of the filter, but here data for the filter are not valid. ", 1=> $additional_en, 2 => "At this moment all of the tasks are shown without the filter."],

        ];

        /********************************************
         * Перевод для контроллера Welcome (RU)
         * Transfer controller Welcome (RU)
         * ******************************************
         */
        $lang['lang_ru']['welcome_controller'] = [
            0   =>  'Добро пожаловать в TimeBig',
            1   =>  $lang['input_form_lang'][0][$segment],
            2   =>  $lang['input_form_lang'][2][$segment],
            4   =>  'Вы вошли под логином - ',
            5   =>  'Пользователя с таким логином и паролем не найдено!',
            6   =>  'Введите все данные в форму!',
            7   =>  'Вы успешно вышли!',
            8   =>  'Востановления пароля в TimeBig',
            9   =>  [
                        'Вы восстановили пароль в TimeBig. <br>Ваш логин: <b>',
                        '</b><br> Ваш новый пароль: <b><i>',
                        '</i></b>'
                    ],
            11  =>  'На указанную почту было отправлено письмо с новым паролем!',
            12  =>  'Ошибка! Не получилось отправить письмо на указанную почту. %s%',
            13  =>  'Ошибка! Не получилось обновить данные. Попробуйте еще раз.',
            14  =>  'Пользователя с таким email не найдено!',
            15  =>  'Введите ваш email в форму!',
            16  =>  'Регистрация нового аккаунта в TimeBig',
            17  =>  $lang['input_form_lang'][1][$segment],
            18  =>  $lang['input_form_lang'][3][$segment],
            19  =>  [
                        ', вы зарегистрировались в TimeBig. <br>Ваш логин: <b>',
                        '</b><br> Ваш пароль: <b><i>',
                        '</i></b>.'
                    ],
            20  =>  ', добро пожаловать в TimeBig!',
            21  =>  'На указанную почту было отправлено письмо с вашими данными и дальнейшими действиями!',
            22  =>  'Ошибка! Произошел сбой при регистрации. Попробуйте еще раз!',
            23  =>  'Страница изменения пароля',
            24  =>   $lang['input_form_lang'][10][$segment].' у Вас не такой!',
            25  =>  'Очень печальная ошибка... Жалко что она произошла... Перезайдите в свой аккаунт!',
            26  =>  'Ваш пароль изменен!',
            27  =>  'Страница изменения профиля',
            28  =>  'Изменение профиля',
            29  =>  'Заполните хотя бы одно из полей! Эта ошибка могла появиться из-за того, что последнее поле у вас неизменилось, а все остальные пустые!',
            30  =>  'Профиль успешно изменен',
            31  =>  'Количество рабочих часов не может быть меньше 1 и больше 14',
            32  =>  "Сколько вы работаете в день часов без учета обеденного перерыва:",
            33  =>  "Максимальный размер аватаки 10 Мб",
            34  =>  "Ваш аватар",
            35  =>  "Сменить аватарку",
            36  =>  "Логин не прошел проверку. Он может содержать латиницу, числа, - и _. Также оно должно быть минимум 5 и максимум 20 символов.",
            37  =>  "Имя не прошло проверку. Оно может содержать латиницу, кириллицу и пробел. Также оно должно быть минимум 3 и максимум 20 символов.",
        ];



        /********************************************
         * Перевод для контроллера Welcome (EN)
         * Transfer controller Welcome (EN)
         * ******************************************
         */
        $lang['lang_en']['welcome_controller'] = [
            0   =>  'Welcome to TimeBig',
            1   =>  $lang['input_form_lang'][0][$segment],
            2   =>  $lang['input_form_lang'][2][$segment],
            4   =>  'You went under the login - ',
            5   =>  'User with such password and username are not found!',
            6   =>  'Enter data into the form!',
            7   =>  'You have successfully logout!',
            8   =>  'Recovering password TimeBig',
            9   =>  [
                'You have restored password TimeBig. <br>Your login: <b>',
                '</b><br> Your new password: <b><i>',
                '</i></b>'
            ],
            11  =>  'On the specified e-mail has been sent a new password!',
            12  =>  'Error! It did not work send an email to the specified email.%s%',
            13  =>  'Error! It did not work to update the data. Try again.',
            14  =>  'No user with this email will not be found!',
            15  =>  'Enter your email address in the form below!',
            16  =>  'Register a new account in TimeBig',
            17  =>  $lang['input_form_lang'][1][$segment],
            18  =>  $lang['input_form_lang'][3][$segment],
            19  =>  [
                ', you are logged in TimeBig. <br>Your login: <b>',
                '</b><br> Your password: <b><i>',
                '</i></b>.'
            ],
            20  =>  ', welcome to TimeBig!',
            21  =>  'On the specified e-mail was sent an email with your data and further actions!',
            22  =>  'Error! There was a failure in the registration. Try again!',
            23  =>  'Change Password page',
            24  =>   $lang['input_form_lang'][10][$segment].' does not match!',
            25  =>  "Very unfortunate mistake ... It's a pity that it happened ... Please reset your account!",
            26  =>  'Your password has been changed!',
            27  =>  'Page profile changes',
            28  =>  'Changing profile',
            29  =>  'Fill in at least one of the fields! This error can occur due to the fact that the last field you unchanged, and the rest are empty!',
            30  =>  'Profile changed successfully',
            31  =>  'Number of working hours can not be less than 1 and more than 14',
            32  =>  "How much are you working hours per day without a lunch break:",
            33  =>  "Maximum file size 10 MB",
            34  =>  "Your avatar",
            35  =>  "Change avatar",
            36  =>  "Login failed validation. It may contain Latin letters, numbers, - and _. Also, it should be at least 5 and a maximum of 20 characters.",
            37  =>  "Name failed validation. It may contain Latin, Cyrillic and space. Also, it should be at least 3 and a maximum of 20 characters.",

        ];



        /********************************************
         * Перевод для общей библиотеки common (RU)
         * Translations for common shared library (RU)
         * ******************************************
         */
        $lang['lang_ru']['common_library'] = [
            0   =>  'Ебанутый логин ваш!',
            1   =>  'Вы уже зашли под логином - ',
            2   =>  'Вы уже зашли в TimeBig',
            4   =>  'Очень странная ошибка... Попробуйте заново авторизоваться!',
            5   =>  'Какие то неправильные куки! Попрошу не издеваться!',
        ];

        /********************************************
         * Перевод для общей библиотеки common (EN)
         * Translations for common shared library (EN)
         * ******************************************
         */
        $lang['lang_en']['common_library'] = [
            0   =>  'Fucked your login!',
            1   =>  "You've come under a login - ",
            2   =>  'You are already logged in TimeBig',
            4   =>  'Very strange error ... Try again to login!',
            5   =>  'What is wrong cookies! I ask not fun!',
        ];




        /********************************************
         * Перевод для контроллера Welcome (RU)
         * Transfer controller Welcome (RU)
         * ******************************************
         */
        $lang['lang_ru']['task_controller'] = [
            0       =>  'Личный кабинет в TimeBig пользователя: <span class="label label-success">%login%</span>',
            1       =>  'Страница добавления нового проекта',
            2       =>  'Файл весит больше 60 Мб',
            3       =>  'Не удалось удалить файл!',
            4       =>  'Такого файла не существует!',
            5       =>  'Ошибка при создании архива из файла',
            6       =>  "Неудалось скопировать файлы: ",
            7       =>  "Не удалось проделать данную операцию",
            8       =>  'Не удалось обновить "Время рабочего дня (в часах и без обеденного перерыва)", поэтому еще раз наберите их, создавая следующую задачу или измените их в профиле.',
            9       =>  'Вы пытаетесь совершить невозможное действие! Не верно переданны параметры в функцию.',
            10      =>  'Вы пытаетесь использовать чужой проект!',
            11      =>  'Выбранный исполнитель не существует или не прикреплен к этому проекту!',
            12      =>  'Просмотр задачи:',
            13      =>  'Такой задачи не существует!',
            14      =>  "Нельзя ставить задачу на паузу, когда вы ее выполнили",
            15      =>  " сек.",
            16      =>  " назад",
            17      =>  " м.",
            18      =>  " ч.",
            19      =>  " д.",
            20      =>  " нед.",
            21      =>  "Досадно... Проект, к которому принадлежит данная задача, недавно был удален, а значит и задача удалена.",
            22      =>  " Теперь вас перекинет на главную страницу через 5 секунд.",
            23      =>  "Задача успешно удалена.",
            24      =>  "Вы не являетесь исполнителем или создателем задачи.",
            25      =>  "Ошибка. Задача не была удалена.",
        ];

        /********************************************
         * Перевод для контроллера Welcome (EN)
         * Transfer controller Welcome (EN)
         * ******************************************
         */
        $lang['lang_en']['task_controller'] = [
            0       =>  'Personal account in TimeBig user: <span class="label label-success">%login%</span>',
            1       =>  'The page for adding a new project',
            2       =>  'The file size more than 60 MB',
            3       =>  'Failed to delete the file!',
            4       =>  'This file does not exist!',
            5       =>  'Error while creating archive file',
            6       =>  "Unable to copy files: ",
            7       =>  "Unable to perform the operation",
            8       =>  'Failed to update the "Time of the day (in hours and no lunch break)", so once again enter them by creating the following problem or change their profile.',
            9       =>  'You are trying to do the impossible action! Not right to pass parameters to the function.',
            10      =>  'You are trying to use someone elses project!',
            11      =>  'The selected artist does not exist or is not attached to the project!',
            12      =>  'Viewing task:',
            13      =>  'This task does not exist!',
            14      =>  "You can not set the task to pause when you performed it",
            15      =>  " s.",
            16      =>  " ago",
            17      =>  " min.",
            18      =>  " h.",
            19      =>  " d.",
            20      =>  " w.",
            21      =>  "Annoyingly... The project to which this task belongs, was removed recently, so and the task is removed.",
            22      =>  " Now will throw you on the homepage through 5 seconds.",
            23      =>  "The task was successfully removed.",
            24      =>  "You aren't the performer or the founder of a task",
            25      =>  "Error. The task was not removed.",

        ];

        /********************************************
         * Перевод для Вьюх (RU) - task_views это для какого контроллера (task)
         * Translation for View (RU) - task_views this for a controller (task)
         * ******************************************
         */
        $lang['lang_ru']['task_views'] = [
            0   =>  'Добавить проект',
            1   =>  'Название проекта',
            2   =>  'Имя или логин ответственного за проект',
            3   =>  'Я ответственный(ая) за проект',
            4   =>  'Проект успешно добавлен',
            5   =>  'Не удалось добавить проект. Обновите страницу и попробуйте еще раз!',
            6   =>  'Возможно я не прав, но вы пытаетесь причинить вред сайту!',
            7   =>  'Мои проекты',
            8   =>  'Прикрепить/удалить персонал',
            9   =>  'Удалить проект',
            10  =>  'Переименовать',
            11  =>  'Название',
            12  =>  'Опции',
            13  =>  'Проект успешно удален',
            14  =>  'Произошла ошибка. Проект не был удален',
            15  =>  'На данный момент вы не прикреплены к какому то проекту, поэтому создайте его или же дождитесь приглашения от вашего админа',
            16  =>  'Нельзя трогать чужие проекты',
            17  =>  'Сохранить',
            18  =>  'Введите имена или логины ваших сотрудников/помощников через пробел',
            19  =>  "Ошибка ввода данных!",
            20  =>  "Причины: <br> - Нельзя оставлять поле пустым. Заполните как минимум 3 символа<br> - Название длиньше чем 255 символов<br> - Допустимые символы: кириллица, латиница, цифры, пробел, - и _",
            21  =>  "Проект переименован",
            22  =>  "Операция прошла успешно",
            23  =>  "Такое название уже существует. Придумайте другое!",
            24  =>  "В функцию не переданно ни одного аргумента!",
            25  =>  "Прикрепить к проекту",
            26  =>  "Все эти люди уже прикреплены к этому проекту",
            27  =>  "Данные успешно обновлены.",
            28  =>  "Вы никого не удалили из проекта",
            29  =>  "Были удалены люди под логинами: ",
            30  =>  "Название проекта",
            31  =>  "Удалить всех",
            32  =>  "Добавить задачу",
            33  =>  "Задача",
            34  =>  "Сложность",
            38  =>  "Название задачи",
            39  =>  "Описание",
            40  =>  "Время рабочего дня (в часах)",
            41  =>  "Можно изменить в профиле",
            44  =>  "Временные рамки задачи",
            45  =>  "Примерное время выполнения",
            46  =>  "Время измерения",
            47  =>  "минуты",
            48  =>  "часы",
            49  =>  "дни",
            50  =>  "недели",
            51  =>  "месяцы",
            52  =>  "Прикрепите картинку/документ:",
            53  =>  "Максимальный размер файла 60 Мб",
            54  =>  "Перетащите файл/файлы в любое место на экране",
            55  =>  "Выбрать файл...",
            56  =>  "Прикрепленные файлы:",
            57  =>  "В прошлый раз вы не добавили задачу. Это то что вы прикрепляли!",
            58  =>  "Закрыть",
            59  =>  "Выберите проект",
            60  =>  "Задача была добавлена!",
            61  =>  "Исполнитель",
            62  =>  "Приоритет",
            63  =>  "Все проекты",
            64  =>  "Статус",
            65  =>  "Поставил",
            66  =>  "Проект: ",
            67  =>  "Вы не создали ни одной задачи! Также возможно вы перешли на несуществующую страницу!",
            68  =>  "Открыть",
            69  =>  "Задача поставлена: ",
            70  =>  "Начало работы: ",
            71  =>  " редактирование задачи",
            72  =>  "Задача выполнена: ",
            73  =>  "Изменить исполнителя",
            74  =>  "Удалить задачу",
            75  =>  "Всего страниц:",
            76  =>  "Номер страницы",
            77  =>  "Перейти на страницу:",
            78  =>  "Фильтры:",
            79  =>  "По статусу:",
            80  =>  "По приоритету:",
            81  =>  "По сложности:",
            82  =>  "Применить фильтры",

            'status_task_0'      =>  'Поставлена',
            'status_task_1'      =>  'Выполняется',
            'status_task_2'      =>  'Выполнена',
            'status_task_3'      =>  'На паузе',

        ];

        /********************************************
         * Перевод для Вьюх (EN) - task_views это для какого контроллера (task)
         * Translation for View (EN) - task_views this for a controller (task)
         * ******************************************
         */
        $lang['lang_en']['task_views'] = [
            0   =>  'Add project',
            1   =>  'Project name',
            2   =>  'Name or login responsible for the project',
            3   =>  'I am responsible for the project',
            4   =>  'The project successfully added',
            5   =>  'Unable to add the project. Refresh the page and try again!',
            6   =>  "Maybe I'm wrong, but you are trying to cause harm to the site!",
            7   =>  'My projects',
            8   =>  'Attach/delete staff',
            9   =>  'Remove project',
            10  =>  'Rename',
            11  =>  'Title',
            12  =>  'Options',
            13  =>  'The project was successfully removed',
            14  =>  'An error has occurred. The project has not been removed',
            15  =>  'At the moment you are not attached to what that project, so create it, or wait for an invitation from your administrator',
            16  =>  "Do not touch other people's projects",
            17  =>  "Save",
            18  =>  'Enter the name or username of your employees/helpers through the space',
            19  =>  "Error input!",
            20  =>  "Reasons: <br> - Do not leave it blank. Fill in at least 3 characters<br> - Name is longer than 255 characters<br> - Allowed characters: Cyrillic, Latin, figures, space, - and _",
            21  =>  "The project was renamed",
            22  =>  "Operation was successfully completed",
            23  =>  "This name already exists. Think different!",
            24  =>  "The function has not submitted any argument!",
            25  =>  "Attach the project",
            26  =>  "All these people are already attached to this project",
            27  =>  "The data have been updated successfully.",
            28  =>  "You have not removed anyone from the project",
            29  =>  "People have been removed under the login: ",
            30  =>  "Project name",
            31  =>  "Remove all",
            32  =>  "Add task",
            33  =>  "Task",
            34  =>  "Complexity",
            38  =>  "Task title",
            39  =>  "Description",
            40  =>  "The time of the day (hours)",
            41  =>  "Can be changed in profile",
            44  =>  "Timeframe task",
            45  =>  "Estimated time to complete",
            46  =>  "Measurement time",
            47  =>  "minutes",
            48  =>  "hours",
            49  =>  "days",
            50  =>  "weeks",
            51  =>  "months",
            52  =>  "Attach image/document:",
            53  =>  "Maximum file size 60 MB",
            54  =>  "Drag the file / files to any location on the screen",
            55  =>  "Choose File...",
            56  =>  "Attached files:",
            57  =>  "Last time you did not add the task. This is something that you have attached!",
            58  =>  "Close",
            59  =>  "Choose a project",
            60  =>  "The task has been added!",
            61  =>  "Performer",
            62  =>  "Priority",
            63  =>  "All projects",
            64  =>  "Status",
            65  =>  "Delivered",
            66  =>  "The project: ",
            67  =>  "You have not created any task! Also, you may have switched to a non-existent page!",
            68  =>  "Open",
            69  =>  "The task posed: ",
            70  =>  "The beginning of work: ",
            71  =>  " editing task",
            72  =>  "Task completed: ",
            73  =>  "To change the performer",
            74  =>  "Delete a task",
            75  =>  "Total pages:",
            76  =>  "Page number",
            77  =>  "Go to page:",
            78  =>  "Filters:",
            79  =>  "By status:",
            80  =>  "By priority:",
            81  =>  "By complexity:",
            82  =>  "Apply filters",

            'status_task_0'      =>  'Supplied',
            'status_task_1'      =>  'Running',
            'status_task_2'      =>  'Implemented',
            'status_task_3'      =>  'Pause',
        ];


        /********************************************
         * Перевод для jquery
         * Translation for jquery
         * ******************************************
         */
        //в js передавать текст без '. Пример НЕ: can't ЛУЧШЕ: cannot
        $lang['lang_ru']['js'] = [
            0   =>  'Вы пытаетесь отправить неправильный запрос!',
            1   =>  "Такого пользователя нет!",
            8   =>  "Ошибка 400: Сервер обработал запрос, но содержание запроса является недействительным.",
            9   =>  "Ошибка 401: Не авторизованный доступ фунеции JavaScript",
            10  =>  "Ошибка 403: Невозможно получить доступ к ресурсу.",
            11  =>  "Ошибка 404: Запрос отправлен на несуществующую страницу",
            12  =>  "Ошибка 500: Внутренняя ошибка сервера.",
            13  =>  "Ошибка 503: Сервис недоступен.",
            14  =>  "Ошибка. Неудалось разборать JSON запрос.",
            15  =>  "Превышен лимит ожидания.",
            16  =>  "Запрос был прерван сервером",
            17  =>  "Название не изменилось. Оставить его в покое?",
            18  =>  "Вы не стали переименовывать проект",
            20  =>  "Вы действительно хотите удалить проект?",
            21  =>  "Удалить",
            22  =>  "Ваш браузер не поддерживает тег audio!",
            23  =>  "Скачать файл",
            24  =>  "Загрузка плеера...",
            26  =>  "Отмена добавления",
            28  =>  "Некорректно заполнено поле",
            29  =>  "Поле",
            30  =>  "не может быть меньше или равно 0",
            31  =>  "не может быть меньше",
            32  =>  "и больше",
            33  =>  "не может быть больше",
            38  =>  "(поле может принимать значение от 1 до 14 часов - без учета обеда)",
            41  =>  "Ошибки:",
            49  =>  'Поле \"Название задачи\" и \"Описание\" должны быть изменены для сохранения',
            50  =>  'Выполнено за: ',
            51  =>  'Поставить на паузу',
            52  =>  'Снять с паузы',

        ];


        //в js передавать текст без '. Пример НЕ: can't ЛУЧШЕ: cannot
        $lang['lang_en']['js'] = [
            0   =>  'You are trying to send a request to the wrong!',
            1   =>  "This user does not have!",
            8   =>  "Error 400: Server understood the request, but request content was invalid.",
            9   =>  "Error 401: Unauthorized access.",
            10  =>  "Error 403: Forbidden resource cannot be accessed.",
            11  =>  "Error 404: Request sent to a nonexistent page",
            12  =>  "Error 500: Internal server error.",
            13  =>  "Error 503: Service unavailable.",
            14  =>  "Error. Parsing JSON Request failed.",
            15  =>  "Request Time out.",
            16  =>  "Request was aborted by the server",
            17  =>  "The name has not changed. Leave him alone?",
            18  =>  "You did not rename the project",
            20  =>  "Are you sure you want to delete the project?",
            21  =>  "Remove",
            22  =>  "Your browser does not support tag audio!",
            23  =>  "Download file",
            24  =>  "Loading the player...",
            26  =>  "Stop adding",
            28  =>  "Incorrectly filled field",
            29  =>  "Field",
            30  =>  "can not be less than or equal to 0",
            31  =>  "can not be less than",
            32  =>  "and more",
            33  =>  "can not be more than",
            38  =>  "(field can have a value from 1 to 14 hours - without dinner)",
            41  =>  "Errors:",
            49  =>  'The \"Task Name\" and \"Description\" should be changed to save',
            50  =>  'Achieved for: ',
            51  =>  'Pause',
            52  =>  'Remove pause',

        ];




        //DON'T TOUCH!!!
        $lang['lang_'.$segment]['languages_desc']       =   $lang['languages_desc'];
        $lang['lang_'.$segment]['input_form_lang']      =   $lang['input_form_lang'];
        $lang['lang_'.$segment]['header_menu_lang']     =   $lang['header_menu_lang'];
        $lang['lang_'.$segment]['js'][2]                =   $lang['lang_'.$segment]['task_views'][14];
        $lang['lang_'.$segment]['js'][3]                =   $lang['lang_'.$segment]['task_views'][10];
        $lang['lang_'.$segment]['js'][4]                =   $lang['lang_'.$segment]['task_views'][17];
        $lang['lang_'.$segment]['js'][5]                =   $lang['lang_'.$segment]['task_views'][19];
        $lang['lang_'.$segment]['js'][6]                =   $lang['lang_'.$segment]['task_views'][20];
        $lang['lang_'.$segment]['js'][7]                =   $lang['lang_'.$segment]['languages_desc'][0]['titleError'][$segment];
        $lang['lang_'.$segment]['js'][19]               =   $lang['lang_'.$segment]['task_views'][18];
        $lang['lang_'.$segment]['js'][25]               =   $lang['lang_'.$segment]['task_views'][32];
        $lang['lang_'.$segment]['js'][27]               =   $lang['lang_'.$segment]['task_views'][38];
        $lang['lang_'.$segment]['js'][34]               =   $lang['lang_'.$segment]['task_views'][59];
        $lang['lang_'.$segment]['js'][35]               =   $lang['lang_'.$segment]['task_views'][34];
        $lang['lang_'.$segment]['js'][36]               =   $lang['lang_'.$segment]['task_views'][40];
        $lang['lang_'.$segment]['js'][39]               =   $lang['lang_'.$segment]['task_views'][45];
        $lang['lang_'.$segment]['js'][40]               =   $lang['lang_'.$segment]['task_views'][46];
        $lang['lang_'.$segment]['js'][42]               =   $lang['lang_'.$segment]['task_views'][61];
        $lang['lang_'.$segment]['js'][43]               =   $lang['lang_'.$segment]['task_views'][62];
        $lang['lang_'.$segment]['js'][44]               =   $lang['lang_'.$segment]['task_views'][58];
        $lang['lang_'.$segment]['js'][45]               =   $lang['lang_'.$segment]['task_views'][68];
        $lang['lang_'.$segment]['js'][46]               =   $lang['lang_'.$segment]['task_views'][64];
        $lang['lang_'.$segment]['js'][47]               =   $lang['lang_'.$segment]['error_code'][1][1];
        $lang['lang_'.$segment]['js'][48]               =   $lang['lang_'.$segment]['task_views'][73];
        $lang['lang_'.$segment]['js'][53]               =   $lang['lang_'.$segment]['task_views'][76];



        $lang['lang_'.$segment]['error_code'][1] = $lang['lang_'.$segment]['error_code'][1][0].$lang['lang_'.$segment]['error_code'][1][1];
		
		return $lang['lang_'.$segment];
	}
}
?>