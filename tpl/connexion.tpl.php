
	<div class="container">

      <form class="form-signin" method="post">
        <h2 class="form-signin-heading">Espace de connexion</h2>
        <input type="hidden" name="action" value="connexion" />
        <label for="inputEmail" class="sr-only">Identifiant</label>
        <input type="text" id="inputLogin" name="login" class="form-control" placeholder="Identifiant" required autofocus>
        <label for="inputPassword" class="sr-only">Mot de passe</label>
        <input type="password" id="inputPassword" name="password" class="form-control" placeholder="Password" required>
        <div class="checkbox">
          <label>
            <input type="checkbox" value="remember-me"> Se souvenir de moi
          </label>
        </div>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Connexion</button>
      </form>

    </div> <!-- /container -->