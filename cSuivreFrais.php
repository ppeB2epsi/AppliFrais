<?php
error_reporting(E_ALL ^ E_DEPRECATED);
/** 
 * Page d'accueil de l'application web AppliFrais
 * @package default
 * @todo  RAS
 */
  $repInclude = './include/';
  require($repInclude . "_init.inc.php");

      // page inaccessible si visiteur non connecté
  if ( ! estComptableConnecte() )
  {
        header("Location: cSeConnecter.php");  
  }
  require($repInclude . "_entete.inc.html");
  require($repInclude . "_sommaire.inc.php");

  $visiteurs = $bdd->obtenirvisiteur();
  $mois = $bdd->obtenirMoisFicheFrais();

  $data = (isset($_POST))? $_POST : "";
  $error = array();

  //Recuperation fiche frais
  if( isset($data["submit"]) )
  {
    $visiteurValid = lireDonneePost("visiteur");
    $moisValid = lireDonneePost("mois");

    // vérification de l'existence de la fiche de frais pour le mois demandé
    $existeFicheFrais = $bdd->existeMontant($moisValid, $visiteurValid);

    // si elle n'existe pas, on la crée avec les élets frais forfaitisés à 0
    if ( !$existeFicheFrais )
    {
        ajouterErreur($tabErreurs, "Pas de fiche de frais pour ce visiteur ce mois");
    }
    else
    {
        // récupération des données sur la fiche de frais demandée
        $ficheFrais = $bdd->suiviMontantTotal($moisValid, $visiteurValid);
       
    }

    
  }
?>

  <!-- Division principale -->
  <div id="contenu">
  
  <div name="droite" style="float:left;width:80%;">
  <div name="bas" style="margin : 10 2 2 2;clear:left;background-color:EE8844;color:white;height:88%;">
    <h1 class="black"> Suivi des frais par visiteur </h1>

    <form name="formValidFrais" method="post" >
   <!--  <form name="formChooseVisiteur" method="post"> -->
    
    <label class="titre">Choisir le visiteur :</label> 
    <select name="visiteur" class="zone">*
    <?php foreach($visiteurs as $item): ?>
      <option value="<?=$item['id']?>" <?= (isset($visiteurValid) && $visiteurValid == $item['id'])? 'selected="selected"' : '';?>><?=$item['nom']?></option>
    <?php endforeach; ?>
    </select>  

    <label class="titre">Mois :</label>
    <select name="mois" class="zone">
      <?php foreach($mois as $item):?>
      <?php 
          $mois = $item["mois"];
          $noMois = intval(substr($mois, 4, 2));
          $annee = intval(substr($mois, 0, 4));
      ?>
        <option value="<?php echo $mois; ?>" <?= (isset($moisValid) && $moisValid == $mois)? 'selected="selected"' : '';?>><?php echo obtenirLibelleMois($noMois) ." ". $annee; ?></option>
      <?php endforeach;?>
    </select>

    <input type="submit" name="submit" value="Valider">

    <?php if(count($tabErreurs) > 0): ?>
    <div class="error">
      <ul>
      <?php foreach($tabErreurs as $item): ?>
        <li><?= $item ?></li>
       <?php endforeach; ?>
      </ul>
    </div>
    <?php endif; ?>
    <!-- </form> -->

    <?php if( (isset($ficheFrais) AND !empty($ficheFrais) ) ): ?>

    <p class="titre" />
    
    <div style="clear:left;"><h2>Frais au forfait </h2></div>
    <table style="color:white;" border="1">
      <tr>
        <th>Montant</th>
        <th>Date </th>
        <th>Situation</th>
      </tr>
      <tr align="center">
        <td width="80" >
          
          <p class='black'><?=$ficheFrais["montantValide"]?></p>
        </td>
        <td width="80"> 
          <p class='black'><?=$ficheFrais["dateModif"];?></p>
        </td> 
        <td width="80"> 
          <select size="2" name="situ">
            <option value="VA">Validé</option>
            <option value="RB">Remboursé</option>
          </select></td>
        </tr>
    </table>
  <?php endif ; ?>
  </form>
  </div>
</div>
  </div>
<?php        
  require($repInclude . "_pied.inc.html");
  require($repInclude . "_fin.inc.php");
?>