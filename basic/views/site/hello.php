<form class="join_form clearform" method="post" action="/user/join">
    <div class="modal-body">
        <label>
            <p>Введите Ваше имя:</p>
            <input minlength="2" maxLength="20" name="name" value="" class="btn"
                   placeholder="Введите Ваше имя" required>
        </label>
        <label>
            <p>Введите e-mail:</p>
            <input type="email" name="email" value="" class="btn" placeholder="Введите Ваш e-mail" required>
        </label>
        <label>
            <p>Введите пароль:</p>
            <input type="password" name="password" value="" placeholder="Введите Ваш  пароль" class="btn"
                   required>
        </label>
        <label>
            <p>Введите пароль:</p>
            <input type="password" name="password" value="" placeholder="Введите Ваш  пароль" class="btn"
                   required>
        </label>
        <label>
            <p>Введите пароль:</p>
            <input type="password" name="password" value="" placeholder="Введите Ваш  пароль" class="btn"
                   required>
        </label>
    </div>
    <p class="notice"></p>
    <div class="modal-footer">
        <input type="submit" name="submit" value="Регистрация">
    </div>

</form>
