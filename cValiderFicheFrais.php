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
  if ( ! estVisiteurConnecte() ) 
  {
        header("Location: cSeConnecter.php");  
  }
  require($repInclude . "_entete.inc.html");
  require($repInclude . "_sommaire.inc.php");

  $visiteurs = $bdd->obtenirvisiteur();
  $mois = $bdd->obtenirMoisFicheFrais();

  //Recuperation fiche frais
  if(!empty(lireDonneePost("visiteur")) && !empty(lireDonneePost("mois")))
  {
    $visiteurSaisi = lireDonneePost("visiteur");
    $moisSaisi = lireDonneePost("mois");

    // vérification de l'existence de la fiche de frais pour le mois demandé
    $existeFicheFrais = $bdd->existeFicheFrais($moisSaisi, $visiteurSaisi);

    // si elle n'existe pas, on la crée avec les élets frais forfaitisés à 0
    if ( !$existeFicheFrais )
    {
        ajouterErreur($tabErreurs, "Le mois demandé est invalide");
        echo "erreur";
    }
    else
    {
        // récupération des données sur la fiche de frais demandée
        $ficheFrais = $bdd->obtenirDetailFicheFrais($moisSaisi, $visiteurSaisi);
        $fraisForfait =  $bdd->obtenirReqEltsForfaitFicheFrais($moisSaisi, $visiteurSaisi);
        $fraisHorsForfait = $bdd->obtenirReqEltsHorsForfaitFicheFrais($moisSaisi, $visiteurSaisi);

        $etape = $fraisForfait[0]['quantite'];
        $kilometrage = $fraisForfait[1]['quantite'];
        $nuitee = $fraisForfait[2]['quantite'];
        $repas = $fraisForfait[3]['quantite'];
    }
  }

?>
  <!-- Division principale -->
  <div id="contenu">
      <h2>Bienvenue sur l'intranet GSB</h2>
      <div name="gauche" style="clear:left:;float:left;width:18%; background-color:white; height:100%;">
<div name="coin" style="height:10%;text-align:center;"><img src="logo.jpg" width="100" height="60"/></div>
<div name="menu" >
  <h2>Outils</h2>
  <ul><li>Frais</li>
    <ul>
      <li><a href="formValidFrais.htm" >Enregistrer opération</a></li>
    </ul>
  </ul>
</div>
</div>
<div name="droite" style="float:left;width:80%;">
  <div name="haut" style="margin: 2 2 2 2 ;height:10%;float:left;"><h1>Validation des Frais</h1></div>  
  <div name="bas" style="margin : 10 2 2 2;clear:left;background-color:EE8844;color:white;height:88%;">
    <h1> Validation des frais par visiteur </h1>
    <form name="formChooseVisiteur" method="post">
    
    <label class="titre">Choisir le visiteur :</label> 
    <select name="visiteur" class="zone">*
    <?php foreach($visiteurs as $item): ?>
      <option value="<?=$item['id']?>" <?= (isset($visiteurSaisi) && $visiteurSaisi == $item['id'])? 'selected="selected"' : '';?>><?=$item['nom']?></option>
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
          <option value="<?php echo $mois; ?>" <?= (isset($moisSaisi) && $moisSaisi == $mois)? 'selected="selected"' : '';?>><?php echo obtenirLibelleMois($noMois) ." ". $annee; ?></option>
        <?php endforeach;?>
    </select>

    <input type="submit" name="submit" value="valider">
    </form>

    <p class="titre" />
    <form name="formValidFrais" method="post" action="enregValidFrais.php">
    <div style="clear:left;"><h2>Frais au forfait </h2></div>
    <table style="color:white;" border="1">
      <tr>
        <th>Repas midi</th>
        <th>Nuitée </th>
        <th>Etape</th>
        <th>Km </th>
        <th>Situation</th>
      </tr>
      <tr align="center">
        <td width="80" > 
          <input type="text" size="3" name="repas" value="<?=$etape?>"/>
        </td>
        <td width="80"> 
          <input type="text" size="3" name="nuitee" value="<?=$kilometrage;?>"/>
        </td> 
        <td width="80"> 
          <input type="text" size="3" name="etape" value="<?=$nuitee;?>"/>
        </td>
        <td width="80"> 
          <input type="text" size="3" name="km" value="<?=$repas;?>"/>
        </td>
        <td width="80"> 
          <select size="3" name="situ">
            <option value="E">Enregistré</option>
            <option value="V">Validé</option>
            <option value="R">Remboursé</option>
          </select></td>
        </tr>
    </table>
    
    <p class="titre" />
    <div style="clear:left;">
      <h2>Hors Forfait</h2>
    </div>
    <table style="color:white;" border="1">
      <tr>
        <th>Date</th>
        <th>Libellé </th>
        <th>Montant</th>
        <th>Situation</th>
      </tr>
      <?php foreach($fraisHorsForfait as $item): ?>
        <tr align="center">
          <input type="hidden" name="idhf" value="<?=$item['date'];?>"/>
          <td width="100" >
            <input type="text" size="12" name="hfDate" value="<?=$item['date'];?>"/>
          </td>
          <td width="220">
            <input type="text" size="30" name="hfLib" value="<?= $item['libelle'];?>"/>
          </td> 
          <td width="90"> 
            <input type="text" size="10" name="hfMont" value="<?= $item['montant'];?>"/>
          </td>
          <td width="80"> 
            <select size="3" name="hfSitu">
              <option value="E">Enregistré</option>
              <option value="V">Validé</option>
              <option value="R">Remboursé</option>
            </select>
          </td>
        </tr>
      <?php endforeach;?>

    </table>    
    <p class="titre"></p>
    <div class="titre">Nb Justificatifs</div>
    <input type="text" class="zone" size="4" name="hcMontant" value="<?=$ficheFrais['nbJustificatifs']?>"/>    
    <p class="titre" /><label class="titre">&nbsp;</label><input class="zone"type="reset" /><input class="zone"type="submit" />
  </form>
  </div>
</div>
  </div>
<?php        
  require($repInclude . "_pied.inc.html");
  require($repInclude . "_fin.inc.php");
?>
