<?php

class Bdd
{

    private $connexion;

    function __construct()
    { // We create the connection
        $this->setDB();
    }

    public function setDB()
    {
        try {
            $this->connexion = new PDO('mysql:host=127.0.0.1;dbname=gsb_frais;charset=utf8', 'root', ''); // we set connection information
            $this->connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }

    public function filtrerChainePourBD($str)
    {
        /*if ( ! get_magic_quotes_gpc() ) {
            // si la directive de configuration magic_quotes_gpc est activ�e dans php.ini,
            // toute cha�ne re�ue par get, post ou cookie est d�j� �chapp�e 
            // par cons�quent, il ne faut pas �chapper la cha�ne une seconde fois                              
            $str = mysql_real_escape_string($str);
        }*/
        return $str;
    }

    public function obtenirDetailVisiteur($id) //select all user ( admin )
    {
        $tab = array(
            'id' => $id
        );
        try {
            
            $req = $this->connexion->prepare('SELECT  id , nom , prenom FROM Visiteur WHERE id = :id '); // we prepared the resquest
            $req->execute($tab);
            $visiteur = $req->fetch();
            return (!empty($visiteur))? $visiteur : false;

        } catch (Exception $e) {
            die('Erreur récupération visiteur : ' . $e->getMessage());
        }
    }

    public function verifierInfosConnexion($login, $mdp) {
        $login = $this->filtrerChainePourBD($login);
        $mdp = $this->filtrerChainePourBD($mdp);

        $tab =  array(
            'login' => $login,
            'mdp' => $mdp,
        );
        try{

            $req = $this->connexion->prepare('SELECT  id, nom , prenom, login, mdp FROM visiteur WHERE login = :login AND mdp = :mdp');
            $req->execute($tab);
            $visiteur = $req->fetch(); 
            return $visiteur;

        } catch (Exception $e){
             die('Erreur connexion : ' . $e->getMessage());
        }
    }

    /** 
     * Ferme la connexion au serveur de donn�es.
     * Ferme la connexion au serveur de donn�es identifi�e par l'identifiant de 
     * connexion $idCnx.
     * @param resource $idCnx identifiant de connexion
     * @return void  
     */
    public function deconnecterServeurBD() {
        $this->connexion = null;
    }


    /** 
     * Fournit les informations d'une fiche de frais. 
     * Retourne les informations de la fiche de frais du mois de $unMois (MMAAAA)
     * sous la forme d'un tableau associatif dont les cl�s sont les noms des colonnes
     * (nbJustitificatifs, idEtat, libelleEtat, dateModif, montantValide).
     * @param resource $idCnx identifiant de connexion
     * @param string $unMois mois demand� (MMAAAA)
     * @param string $unIdVisiteur id visiteur  
     * @return array tableau associatif de la fiche de frais
     */
    public function obtenirDetailFicheFrais($unMois, $unIdVisiteur) {
        $unMois = filtrerChainePourBD($unMois);

        $tab = array(
            'mois' => $unMois,
            'idVisiteur' => $unIdVisiteur,
            );

        $req = $this->connexion->prepare("SELECT IFNULL(nbJustificatifs,0) AS nbJustificatifs, Etat.id AS idEtat, libelle AS libelleEtat, dateModif, montantValide 
                FROM FicheFrais INNER JOIN Etat ON idEtat = Etat.id 
                WHERE idVisiteur = :idVisiteur and mois = :mois
            ");

        $req->execute($tab);
        $result = $req->fetchAll();
        return $result;
    }                  
    /** 
     * V�rifie si une fiche de frais existe ou non. 
     * Retourne true si la fiche de frais du mois de $unMois (MMAAAA) du visiteur 
     * $idVisiteur existe, false sinon. 
     * @param resource $idCnx identifiant de connexion
     * @param string $unMois mois demand� (MMAAAA)
     * @param string $unIdVisiteur id visiteur  
     * @return bool�en existence ou non de la fiche de frais
     */
    public function existeFicheFrais( $unMois, $unIdVisiteur) {
        $unMois = $this->filtrerChainePourBD($unMois);

        $tab = array(

            'mois' => $unMois,
            'idVisiteur' => $unIdVisiteur,

            );

        $req = $this->connexion->prepare("SELECT idVisiteur FROM ficheFrais where idVisiteur = :idVisiteur 
                  AND mois = :mois");

        $req->execute($tab);
        $result = $req->fetchAll();
        return  is_array($result);
        }        
        
    /** 
     * Fournit le mois de la derni�re fiche de frais d'un visiteur.
     * Retourne le mois de la derni�re fiche de frais du visiteur d'id $unIdVisiteur.
     * @param resource $idCnx identifiant de connexion
     * @param string $unIdVisiteur id visiteur  
     * @return string dernier mois sous la forme AAAAMM
     */
    public function obtenirDernierMoisSaisi($unIdVisiteur) {

        $tab = array(

            'idVisiteur' => $unIdVisiteur

            );

        $req = $this->connexion->prepare("SELECT max(mois) AS dernierMois FROM FicheFrais WHERE idVisiteur= :idVisiteur ");


        $req->execute($tab);
        $result = $req->fetchAll();
        $dernierMois = $result["dernierMois"];

        return $dernierMois;
    }

    /** 
     * Ajoute une nouvelle fiche de frais et les �l�ments forfaitis�s associ�s, 
     * Ajoute la fiche de frais du mois de $unMois (MMAAAA) du visiteur 
     * $idVisiteur, avec les �l�ments forfaitis�s associ�s dont la quantit� initiale
     * est affect�e � 0. Cl�t �ventuellement la fiche de frais pr�c�dente du visiteur. 
     * @param resource $idCnx identifiant de connexion
     * @param string $unMois mois demand� (MMAAAA)
     * @param string $unIdVisiteur id visiteur  
     * @return void
     */
    public function ajouterFicheFrais($unMois, $unIdVisiteur) {
        try {
            $unMois = $this->filtrerChainePourBD($unMois);
            // modification de la derni�re fiche de frais du visiteur
            $dernierMois = $this->obtenirDernierMoisSaisi($unIdVisiteur);
            $laDerniereFiche = $this->obtenirDetailFicheFrais($dernierMois, $unIdVisiteur);
            if (is_array($laDerniereFiche) && $laDerniereFiche['idEtat'] == 'CR') {
                $this->modifierEtatFicheFrais($dernierMois, $unIdVisiteur, 'CL');
            }

            // ajout de la fiche de frais � l'�tat Cr��
            $sql = "insert into fichefrais (idVisiteur, mois, nbJustificatifs, montantValide, idEtat, dateModif) values ('"
                . $unIdVisiteur
                . "','" . $unMois . "',0,NULL, 'CR', '" . date("Y-m-d") . "')";
            $req = $this->connexion->prepare($sql);
            $req->execute();


            // ajout des �l�ments forfaitis�s
            $sql = "select id from FraisForfait";
            $idJeuRes = $this->connexion->prepare($sql);
            $idJeuRes->execute();

            if ($idJeuRes) {
                $ligne = $idJeuRes->fetchAll();
                while (is_array($ligne)) {
                    $idFraisForfait = $ligne["id"];
                    // insertion d'une ligne frais forfait dans la base
                    $sql = "insert into LigneFraisForfait (idVisiteur, mois, idFraisForfait, quantite)
                            values ('" . $unIdVisiteur . "','" . $unMois . "','" . $idFraisForfait . "',0)";
                    $req = $this->connexion->prepare($sql);
                    $req->execute();
                    // passage au frais forfait suivant
                    $ligne = $idJeuRes->fetchAll();
                }
            }
        }
        catch (Exception $e){
            die('Erreur ajout fiche frais: ' . $e->getMessage());
        }
    }

    /**
     * Retourne le texte de la requ�te select concernant les mois pour lesquels un 
     * visiteur a une fiche de frais. 
     * 
     * La requ�te de s�lection fournie permettra d'obtenir les mois (AAAAMM) pour 
     * lesquels le visiteur $unIdVisiteur a une fiche de frais. 
     * @param string $unIdVisiteur id visiteur  
     * @return string texte de la requ�te select
     */                                                 
    public function obtenirReqMoisFicheFrais($unIdVisiteur) {

        $tab = array(

            'idVisiteur' => $unIdVisiteur

            );

        $req = $this->connexion->prepare("SELECT fichefrais.mois AS mois FROM  fichefrais WHERE fichefrais.idvisiteur =
                :idVisiteur ORDER BY fichefrais.mois DESC ");

        $req->execute($tab);
        $result = $req->fetchAll();
        return $result;
    }
     
                      
    /**
     * Retourne le texte de la requ�te select concernant les �l�ments forfaitis�s 
     * d'un visiteur pour un mois donn�s. 
     * 
     * La requ�te de s�lection fournie permettra d'obtenir l'id, le libell� et la
     * quantit� des �l�ments forfaitis�s de la fiche de frais du visiteur
     * d'id $idVisiteur pour le mois $mois    
     * @param string $unMois mois demand� (MMAAAA)
     * @param string $unIdVisiteur id visiteur  
     * @return string texte de la requ�te select
     */                                                 
    public function obtenirReqEltsForfaitFicheFrais($unMois, $unIdVisiteur) {
        $unMois = $this->filtrerChainePourBD($unMois);
        $sql = "select idFraisForfait, libelle, quantite from LigneFraisForfait
                  inner join FraisForfait on FraisForfait.id = LigneFraisForfait.idFraisForfait
                  where idVisiteur='" . $unIdVisiteur . "' and mois='" . $unMois . "'";
        try {
            $req = $this->connexion->prepare($sql);
            $req->execute();
            $result = $req->fetchAll();
            return $result;
        }
        catch (Exception $e){
            die('Erreur obtenir fiche frais : ' . $e->getMessage());
        }
    }

    /**
     * Retourne le texte de la requ�te select concernant les �l�ments hors forfait 
     * d'un visiteur pour un mois donn�s. 
     * 
     * La requ�te de s�lection fournie permettra d'obtenir l'id, la date, le libell� 
     * et le montant des �l�ments hors forfait de la fiche de frais du visiteur
     * d'id $idVisiteur pour le mois $mois    
     * @param string $unMois mois demand� (MMAAAA)
     * @param string $unIdVisiteur id visiteur  
     * @return string texte de la requ�te select
     */                                                 
    public function obtenirReqEltsHorsForfaitFicheFrais($unMois, $unIdVisiteur) {
        $unMois = $this->filtrerChainePourBD($unMois);
        $sql = "select id, date, libelle, montant from LigneFraisHorsForfait
                  where idVisiteur='" . $unIdVisiteur 
                  . "' and mois='" . $unMois . "'";
        $req = $this->connexion->prepare($sql);
        $req->execute();
        $result = $req->fetchAll();
        return $result;
    }

    /**
     * Supprime une ligne hors forfait.
     * Supprime dans la BD la ligne hors forfait d'id $unIdLigneHF
     * @param resource $idCnx identifiant de connexion
     * @param string $idLigneHF id de la ligne hors forfait
     * @return void
     */
    public function supprimerLigneHF($unIdLigneHF) {
        $sql = "delete from LigneFraisHorsForfait where id = " . $unIdLigneHF;
        $req = $this->connexion->prepare($sql);
        $req->execute();
    }

    /**
     * Ajoute une nouvelle ligne hors forfait.
     * Ins�re dans la BD la ligne hors forfait de libell� $unLibelleHF du montant 
     * $unMontantHF ayant eu lieu � la date $uneDateHF pour la fiche de frais du mois
     * $unMois du visiteur d'id $unIdVisiteur
     * @param resource $idCnx identifiant de connexion
     * @param string $unMois mois demand� (AAMMMM)
     * @param string $unIdVisiteur id du visiteur
     * @param string $uneDateHF date du frais hors forfait
     * @param string $unLibelleHF libell� du frais hors forfait 
     * @param double $unMontantHF montant du frais hors forfait
     * @return void
     */
    public function ajouterLigneHF($unMois, $unIdVisiteur, $uneDateHF, $unLibelleHF, $unMontantHF) {
        $unLibelleHF = $this->filtrerChainePourBD($unLibelleHF);
        $uneDateHF = $this->filtrerChainePourBD(convertirDateFrancaisVersAnglais($uneDateHF));
        $unMois = $this->filtrerChainePourBD($unMois);

        try {
            $sql = "insert into LigneFraisHorsForfait(idVisiteur, mois, date, libelle, montant)
                    values ('" . $unIdVisiteur . "','" . $unMois . "','" . $uneDateHF . "','" . $unLibelleHF . "'," . $unMontantHF . ")";
            $req = $this->connexion->prepare($sql);
            $req->execute();
        }
        catch (Exception $e){
            die('Erreur ajout ligne HF : ' . $e->getMessage());
        }
    }

    /**
     * Modifie les quantit�s des �l�ments forfaitis�s d'une fiche de frais. 
     * Met � jour les �l�ments forfaitis�s contenus  
     * dans $desEltsForfaits pour le visiteur $unIdVisiteur et
     * le mois $unMois dans la table LigneFraisForfait, apr�s avoir filtr� 
     * (annul� l'effet de certains caract�res consid�r�s comme sp�ciaux par 
     *  MySql) chaque donn�e   
     * @param resource $idCnx identifiant de connexion
     * @param string $unMois mois demand� (MMAAAA) 
     * @param string $unIdVisiteur  id visiteur
     * @param array $desEltsForfait tableau des quantit�s des �l�ments hors forfait
     * avec pour cl�s les identifiants des frais forfaitis�s 
     * @return void  
     */
    public function modifierEltsForfait($unMois, $unIdVisiteur, $desEltsForfait) {
        $unMois= $this->filtrerChainePourBD($unMois);
        $unIdVisiteur= $this->filtrerChainePourBD($unIdVisiteur);
        foreach ($desEltsForfait as $idFraisForfait => $quantite) {
            $sql = "update LigneFraisForfait set quantite = " . $quantite
                        . " where idVisiteur = '" . $unIdVisiteur . "' and mois = '"
                        . $unMois . "' and idFraisForfait='" . $idFraisForfait . "'";
            try {
                $req = $this->connexion->prepare($sql);
                $req->execute();

            }
            catch (Exception $e){
                die('Erreur modifEltsForfait : ' . $e->getMessage());
            }
        }
    }
    /**
     * Modifie l'�tat et la date de modification d'une fiche de frais
     * Met � jour l'�tat de la fiche de frais du visiteur $unIdVisiteur pour
     * le mois $unMois � la nouvelle valeur $unEtat et passe la date de modif � 
     * la date d'aujourd'hui
     * @param resource $idCnx identifiant de connexion
     * @param string $unIdVisiteur 
     * @param string $unMois mois sous la forme aaaamm
     * @return void 
     */
    public function modifierEtatFicheFrais($unMois, $unIdVisiteur, $unEtat) {
        $sql = "update FicheFrais set idEtat = '" . $unEtat .
                   "', dateModif = now() where idVisiteur ='" .
                   $unIdVisiteur . "' and mois = '". $unMois . "'";
        $req = $this->connexion->prepare($sql);
        $req->execute();
    }             
}
?>