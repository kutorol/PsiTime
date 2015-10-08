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
         * Email Администратора ну или просто сутенера этого сайта
         */
        $lang['emailAdminSite'] = 'admin@timebig.ru';

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
                                                'ru'    =>  ['first_link'   =>  'Первая', 'last_link' =>  'Последняя'],
                                                'en'    =>  ['first_link'   =>  'First', 'last_link' =>  'Last'],
                                                //'de'  =>  ['first_link'   =>  'Erste', 'last_link' =>  'Letzte'],
                                            ],
                        'errorAuth'     =>  [
                                                'ru'=>'Вам необходимо авторизоваться!',
                                                'en'=>'Please log in!'
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

        /********************************************
         * Перевод для контроллера Welcome (RU)
         * Transfer controller Welcome (RU)
         * ******************************************
         */
        $lang['lang_ru']['welcome_controller'] = [
            0   =>  'Добро пожаловать в TimeBig',
            1   =>  $lang['input_form_lang'][0][$segment],
            2   =>  $lang['input_form_lang'][2][$segment],
            3   =>  'На данный момент вам нужно дождаться активации от администратора!',
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
            12  =>  'Ошибка! Не получилось отправить письмо на указанную почту.%s%',
            13  =>  'Ошибка! Не получилось обновить данные. Попробуйте еще раз.',
            14  =>  'Пользователя с таким email не найдено!',
            15  =>  'Введите ваш email в форму!',
            16  =>  'Регистрация нового аккаунта в TimeBig',
            17  =>  $lang['input_form_lang'][1][$segment],
            18  =>  $lang['input_form_lang'][3][$segment],
            19  =>  [
                        ', вы зарегистрировались в TimeBig. <br>Ваш логин: <b>',
                        '</b><br> Ваш пароль: <b><i>',
                        '</i></b>. <br>'
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
            29  =>  'Заполните хотя бы одно из полей!',
            30  =>  'Профиль успешно изменен',
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
            3   =>  'At this point you need to wait for the activation of the administrator!',
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
                '</i></b>. <br>'
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
            29  =>  'Fill in at least one of the fields!',
            30  =>  'Profile changed successfully',
        ];

        $lang['lang_'.$segment]['welcome_controller'][19][2] .= $lang['lang_'.$segment]['welcome_controller'][3];


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
        $lang['lang_'.$segment]['common_library'][3] = $lang['lang_'.$segment]['welcome_controller'][3];




        /********************************************
         * Перевод для контроллера Welcome (RU)
         * Transfer controller Welcome (RU)
         * ******************************************
         */
        $lang['lang_ru']['task_controller'] = [
            0   =>  'Личный кабинет в TimeBig пользователя: <span class="label label-success">%login%</span>',
            1   =>  'Страница добавления нового проекта',
        ];

        /********************************************
         * Перевод для контроллера Welcome (EN)
         * Transfer controller Welcome (EN)
         * ******************************************
         */
        $lang['lang_en']['task_controller'] = [
            0   =>  'Personal account in TimeBig user: <span class="label label-success">%login%</span>',
            1   =>  'The page for adding a new project',
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
            8   =>  'Прикрепить персонал',
            9   =>  'Удалить проект',
            10  =>  'Переименовать',
            11  =>  'Название',
            12  =>  'Опции',
            13  =>  'Проект успешно удален',
            14  =>  'Произошла ошибка. Проект не был удален',
            15  =>  'На данный момент вы не прикреплены к какому то проекту, поэтому создайте его или же дождитесь приглашения от вашего админа',
            16  =>  'Нельзя удалять чужие проекты',
            17  =>  'Сохранить',
            18  =>  'Введите имена или логины ваших сотрудников/помощников',
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
            8   =>  'Attach staff',
            9   =>  'Remove project',
            10  =>  'Rename',
            11  =>  'Title',
            12  =>  'Options',
            13  =>  'The project was successfully removed',
            14  =>  'An error has occurred. The project has not been removed',
            15  =>  'At the moment you are not attached to what that project, so create it, or wait for an invitation from your administrator',
            16  =>  "You can not delete other people's projects",
            17  =>  "Save",
            18  =>  'Enter the name or username of your employees/helpers',
        ];


        /********************************************
         * Перевод для jquery
         * Translation for jquery
         * ******************************************
         */
        $lang['lang_ru']['js'] = [
            0   =>  'Вы пытаетесь отправить неправильный запрос!',
            1   =>  "Такого пользователя нет!",
        ];

        $lang['lang_en']['js'] = [
            0   =>  'You are trying to send a request to the wrong!',
            1   =>  "This user does not have!",
        ];




        //DON'T TOUCH!!!
        $lang['lang_'.$segment]['languages_desc']       =   $lang['languages_desc'];
        $lang['lang_'.$segment]['emailAdminSite']       =   $lang['emailAdminSite'];
        $lang['lang_'.$segment]['input_form_lang']      =   $lang['input_form_lang'];
        $lang['lang_'.$segment]['header_menu_lang']     =   $lang['header_menu_lang'];
        $lang['lang_'.$segment]['js'][2]                =   $lang['lang_'.$segment]['task_views'][14];
        $lang['lang_'.$segment]['js'][3]                =   $lang['lang_'.$segment]['task_views'][10];
        $lang['lang_'.$segment]['js'][4]                =   $lang['lang_'.$segment]['task_views'][17];



		
		return $lang['lang_'.$segment];
	}
}
?>