<?php
$repInclude = './include/';
require($repInclude . "_init.inc.php");
/**
 * Created by PhpStorm.
 * User: Alexis Varnieu
 * Date: 03/03/2015
 * Time: 08:53
 */
// Appel de la librairie FPDF
require("fpdf/fpdf.php");
// Création de la class PDF
class PDF extends FPDF {

    // Header
    function Header() {
        // Logo
        $this->Image('images/logo.jpg',8,2,80);
        // Saut de ligne
        $this->Ln(20);
    }
    // Footer
    function Footer() {
        // Positionnement à 1,5 cm du bas
        $this->SetY(-15);
        // Adresse
        $this->Cell(196,5,'GSB-'.$_GET['moischoisi'].'',0,0,'C');
    }
}
// Activation de la classe
$pdf = new PDF('P','mm','A4');
$pdf->AddPage();
$pdf->SetFont('Helvetica','',11);
$pdf->SetTextColor(0);


$row1 = $bdd->donneVisiteur();
$km = $bdd->donnefichefrais($_GET['moischoisi'],'KM');
$etp =  $bdd->donnefichefrais($_GET['moischoisi'],'ETP');
$nui =  $bdd->donnefichefrais($_GET['moischoisi'],'NUI');
$rep =  $bdd->donnefichefrais($_GET['moischoisi'],'REP');
$horsforfait = $bdd->donnefichehorsfrais($_GET['moischoisi']);
//var_dump($horsforfait);
// Infos du client calées à droite
$pdf->Text(120,38,utf8_decode($row1['prenom']).' '.utf8_decode($row1['nom']));
$pdf->Text(120,43,utf8_decode($row1['adresse']));
$pdf->Text(120,48,$row1['cp'].' '.utf8_decode($row1['ville']));

// Position de l'entête à 10mm des infos (48 + 10)
$position_entete = 68;
$position_entete2 = 98;

function entete_table($position_entete){
    global $pdf;
    $pdf->Text(8,65,'Quantités des éléments forfaitisés :');
    $pdf->SetDrawColor(183); // Couleur du fond
    $pdf->SetFillColor(221); // Couleur des filets
    $pdf->SetTextColor(0); // Couleur du texte
    $pdf->SetY($position_entete);
    $pdf->SetX(8);
    $pdf->Cell(50,8,'Forfait Etape',1,0,'L',1);
    $pdf->SetX(58); // 8 + 96
    $pdf->Cell(50,8,'Frais Kilométrique',1,0,'L',1);
    $pdf->SetX(108); // 104 + 10
    $pdf->Cell(50,8,'Nuitée Hotel',1,0,'L',1);
    $pdf->SetX(158);
    $pdf->Cell(50,8,'Repas Restaurant',1,0,'L',1);
    $pdf->Ln(); // Retour à la ligne
}

// Liste des détails
$position_detail = 76; // Position à 8mm de l'entête


$pdf->SetY($position_detail);
$pdf->SetX(8);
$pdf->MultiCell(50,8,utf8_decode($etp['quantite']),1,'L');
$pdf->SetY($position_detail);
$pdf->SetX(58);
$pdf->MultiCell(50,8,$km['quantite'],1,'L');
$pdf->SetY($position_detail);
$pdf->SetX(108);
$pdf->MultiCell(50,8,$nui['quantite'],1,'L');
$pdf->SetY($position_detail);
$pdf->SetX(158);
$pdf->MultiCell(50,8,$rep['quantite'],1,'L');
$position_detail += 8;
$i=106;
$pdf->Text(8,95,'Descriptif des éléments hors forfait :');
foreach($horsforfait as $horsforfait)
{

    $pdf->SetDrawColor(183); // Couleur du fond
    $pdf->SetFillColor(221); // Couleur des filets
    $pdf->SetTextColor(0); // Couleur du texte
    $pdf->SetY($position_entete2);
    $pdf->SetX(8);
    $pdf->Cell(50,8,'Date',1,0,'L',1);
    $pdf->SetX(58); // 104 + 10
    $pdf->Cell(50,8,'Libellé',1,0,'L',1);
    $pdf->SetX(108);
    $pdf->Cell(50,8,'Montant',1,0,'L',1);
    $pdf->Ln();
    $position_detail2 = $i; // Position à 8mm de l'entête

    $pdf->SetY($position_detail2);
    $pdf->SetX(8);
    $pdf->MultiCell(50,8,$horsforfait['date'],1,'L');
    $pdf->SetY($position_detail2);
    $pdf->SetX(58);
    $pdf->MultiCell(50,8,$horsforfait['libelle'],1,'L');
    $pdf->SetY($position_detail2);
    $pdf->SetX(108);
    $pdf->MultiCell(50,8,$horsforfait['montant'],1,'L');
    $position_detail += 8;
    $i=$i+8;
}

entete_table($position_entete);
// Nom du fichier
$nom = 'FicheFrais'.$_GET['moischoisi'].'.pdf';

// Création du PDF
$pdf->Output($nom,'D');
?>