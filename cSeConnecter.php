<?php  
/** 
 * Script de contrôle et d'affichage du cas d'utilisation "Se connecter"
 * @package default
 * @todo  RAS
 */
  $repInclude = './include/';
  require($repInclude . "_init.inc.php");
  
  // est-on au 1er appel du programme ou non ?
  $etape=(count($_POST)!=0)?'validerConnexion' : 'demanderConnexion';
  if ($etape=='validerConnexion')
  { // un client demande à s'authentifier
      // acquisition des données envoyées, ici login et mot de passe
      $login = lireDonneePost("txtLogin");
      $mdp = lireDonneePost("txtMdp");
      $lgUser = $bdd->verifierInfosConnexion($login);
      // si l'id utilisateur a été trouvé, donc informations fournies sous forme de tableau
      //if ( is_array($lgUser) )
      //{
          if (password_verify( $mdp, $lgUser['mdp']))
          {
              $comtable ='non';
              affecterInfosConnecte($lgUser["id"], $lgUser["login"], $comtable);
          }
          else{
              $lgUser = $bdd->verifierInfosConnexionComptable($login);
              if (password_verify( $mdp, $lgUser['mdp']))
              {
                  $comtable = 'oui';
                  affecterInfosConnecte($lgUser["id"], $lgUser["login"], $comtable);
              }
              else{
                  ajouterErreur($tabErreurs, "Pseudo et/ou mot de passe incorrects");
              }
          }
      //}
      //else
      //{
         // ajouterErreur($tabErreurs, "Pseudo et/ou mot de passe incorrects");
      //}
  }
  if ( $etape == "validerConnexion" && nbErreurs($tabErreurs) == 0 )
  {
        header("Location:cAccueil.php");
  }

  require($repInclude . "_entete.inc.html");
  //require($repInclude . "_sommaire.inc.php");
  
?>
<!-- Division pour le contenu principal -->
    <div id="contenu">
      <h2>Identification utilisateur</h2>
<?php
          if ( $etape == "validerConnexion" ) 
          {
              if ( nbErreurs($tabErreurs) > 0 ) 
              {
                echo toStringErreurs($tabErreurs);
              }
          }
?>               
      <form id="frmConnexion" name="Connexion" action="" method="post">
      <div class="corpsForm">
        <input type="hidden" name="etape" id="etape" value="validerConnexion" />
      <p>
        <label for="txtLogin" accesskey="n">* Login : </label>
        <input type="text" id="txtLogin" name="txtLogin" maxlength="20" size="15" value="" title="Entrez votre login" />
      </p>
      <p>
        <label for="txtMdp" accesskey="m">* Mot de passe : </label>
        <input type="password" id="txtMdp" name="txtMdp" maxlength="15" size="15" value=""  title="Entrez votre mot de passe"/>
      </p>
      </div>
      <div class="piedForm">
      <p>
        <input type="submit" name="ok" id="ok" value="Valider" />
        <input type="reset" name="annuler" id="annuler" value="Effacer" />
      </p> 
      </div>
      </form>
    </div>
<?php
    require($repInclude . "_pied.inc.html");
    require($repInclude . "_fin.inc.php");
?>