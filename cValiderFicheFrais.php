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

   // validation fiche Frais
  if(isset($data["submit"]) && $data["submit"] == "Envoyer")
  {
    $visiteurValid = $data['visiteur']; //
    $moisValid = $data['mois']; //

    //reformatage des données post pour les lignes hors forfait
    foreach ($data as $key => $item) 
    {
      $parts = explode('-', $key);
      if($parts[0] === 'hf')
      {
        $horsForfait[$parts[1]]['situ'] = $item;
        $horsForfait[$parts[1]]['libelle'] = $parts[2];
        $horsForfait[$parts[1]]['montant'] = intval($parts[3]);
      }
    }

    //
    if(isset($data['situ']) AND !empty($data['situ']))
    {
      $bdd->modifierEtatFicheFrais($moisValid, $visiteurValid, $data["situ"]);
    }
    else
    {
      ajouterErreur($tabErreurs, "L'état de la fiche de frais doit être renseigné");
    }

    //
    if((isset($data['etape']) AND !empty($data['etape'])) AND (isset($data['km']) AND !empty($data['km'])) AND (isset($data['nuitee']) AND !empty($data['nuitee'])) AND (isset($data['repas']) AND !empty($data['repas'])) ) 
    {
      $elementsValid = array(
        'ETP' => $data['etape'],
        'KM'  => $data['km'],
        'NUI' => $data['nuitee'],
        'REP' => $data['repas'],
      );

      $bdd->modifierEltsForfait($moisValid, $visiteurValid, $elementsValid);

      $result = $bdd->obtenirfraisforfait();
        $lignefraisforfait = $bdd->obtenirLignesFicheFrais($moisValid, $visiteurValid);
        $user = $bdd->obtenirDetailVisiteur($visiteurValid);
        $vehicule = $bdd->obtenirDetailVehicule($user['idVehicule']);

        foreach ($lignefraisforfait as $lignes)
        {foreach ($result as $ligne){

            if ($lignes['idFraisForfait'] == 'ETP' AND $ligne['id'] == 'ETP')
            {
                $etape = $lignes['quantite'] * $ligne['montant'];
                echo $etape;echo '<br>';
            }
            else if ($lignes['idFraisForfait'] == 'KM' AND $ligne['id'] == 'KM')
            {
                $km = $lignes['quantite'] * $vehicule['prix'];
                echo $km;echo '<br>';
            }
            else if ($lignes['idFraisForfait'] == 'NUI' AND $ligne['id'] == 'NUI')
            {
                $nuit = $lignes['quantite'] * $ligne['montant'];
                echo $nuit;echo '<br>';
            }
            else if ($lignes['idFraisForfait'] == 'REP' AND $ligne['id'] == 'REP')
            {
                $repas = $lignes['quantite'] * $ligne['montant'];
                echo $repas;echo '<br>';
            }

        }}

        $montantHorsForfait = 0;

        foreach ($horsForfait as $item) 
        {
            if($item['situ'] == 'valid')
            {
              $montantHorsForfait += $item['montant'];
            }
        }

        $montant = $etape + $km + $nuit + $repas + $montantHorsForfait;
        $bdd->modifierMontantFicheFrais($moisValid, $visiteurValid, $montant);
    }
    else
    {
      ajouterErreur($tabErreurs, "Les elements de la fiche de frais doivent être renseignés");
    }

    //Gestion des refus des lignes hors forfaits
    if(isset($horsForfait) AND count($horsForfait) > 0)
    {
      foreach ($horsForfait as $key => $item)
      {
          if(isset($item['situ']) AND isset($item['libelle']))
          {
              $bdd->modifierHorsForfait($key, $item['situ'], $item['libelle']);
          }
      }
    }
   
  }

  //Recuperation fiche frais
  if( isset($data["submit"]))
  {
    $visiteurValid = lireDonneePost("visiteur");
    $moisValid = lireDonneePost("mois");

    // vérification de l'existence de la fiche de frais pour le mois demandé
    $existeFicheFrais = $bdd->existeFicheFrais($moisValid, $visiteurValid);

    // si elle n'existe pas, on la crée avec les élets frais forfaitisés à 0
    if ( !$existeFicheFrais )
    {
        ajouterErreur($tabErreurs, "Pas de fiche de frais pour ce visiteur ce mois");
    }
    else
    {
        // récupération des données sur la fiche de frais demandée
        $ficheFrais = $bdd->obtenirDetailFicheFrais($moisValid, $visiteurValid);
        $fraisForfait = $bdd->obtenirReqEltsForfaitFicheFrais($moisValid, $visiteurValid);
        $fraisHorsForfait = $bdd->obtenirReqEltsHorsForfaitFicheFrais($moisValid, $visiteurValid);

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
  <div name="droite" style="float:left;width:80%;">
  <div name="haut" style="margin: 2 2 2 2 ;height:10%;float:left;"><h1>Validation des Frais</h1></div>  
  <div name="bas" style="margin : 10 2 2 2;clear:left;background-color:EE8844;color:white;height:88%;">
    <h1 class="black"> Validation des frais par visiteur </h1>

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

    <?php if( (isset($ficheFrais) AND !empty($ficheFrais) ) AND ( isset($fraisForfait) AND !empty($fraisForfait) )): ?>

    <p class="titre" />
    
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
          <input type="text" size="3" name="repas" value="<?=$repas?>"/>
        </td>
        <td width="80"> 
          <input type="text" size="3" name="nuitee" value="<?=$nuitee;?>"/>
        </td> 
        <td width="80"> 
          <input type="text" size="3" name="etape" value="<?=$etape;?>"/>
        </td>
        <td width="80"> 
          <input type="text" size="3" name="km" value="<?=$kilometrage;?>"/>
        </td>
        <td width="80"> 
          <select size="3" name="situ">
            <option value="CR">Enregistré</option>
            <option value="VA">Validé</option>
            <option value="RB">Remboursé</option>
          </select></td>
        </tr>
    </table>


    <?php if(count($fraisHorsForfait) > 0): ?> 
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
          <td width="100" >
            <p class="zone black"><?= $item['date'];?></p>
          </td>
          <td width="220">
            <p class="zone black"><?= $item['libelle'];?></p>
          </td> 
          <td width="90"> 
            <p class="zone black"><?= $item['montant'];?></p>
          </td>
          <td width="80"> 
            <select size="3" name="<?="hf"."-".$item['id']."-".$item['libelle']."-".$item['montant'];?>">
              <option value="Valide">Validé</option>
              <option value="Refuse">Supression</option>
            </select>
          </td>
        </tr>
      <?php endforeach;?>

    </table>
  <?php endif; ?>
    <p class="titre"></p>
    <div class="titre black">Nb Justificatifs</div>
    <input type="text" class="zone" size="4" name="hcMontant" value="<?=$ficheFrais['nbJustificatifs']?>"/>
    <div class="titre black">Montant</div>
    <p class="zone black"><?= $ficheFrais['montantValide']; ?></p>   
    <p class="titre" />
    <label class="titre">&nbsp;</label>
    <input class="zone" type="reset" />
    <input class="zone" type="submit" name="submit" value="Envoyer" />
  <?php endif; ?>
  </form>
  </div>
</div>
  </div>
<?php        
  require($repInclude . "_pied.inc.html");
  require($repInclude . "_fin.inc.php");
?>
