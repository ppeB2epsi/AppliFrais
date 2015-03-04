<?php
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
        $this->Cell(196,5,'Mes coordonnées - Mon téléphone',0,0,'C');
    }
}
// Activation de la classe
$pdf = new PDF('P','mm','A4');
$pdf->AddPage();
$pdf->SetFont('Helvetica','',11);
$pdf->SetTextColor(0);
/*

$row1 = donneVisiteur();

// Infos du client calées à droite
$pdf->Text(120,38,utf8_decode($row1['prenom']).' '.utf8_decode($row1['nom']));
$pdf->Text(120,43,utf8_decode($row1['adresse']));
$pdf->Text(120,48,$row1['cp'].' '.utf8_decode($row1['ville']));
*/

// Nom du fichier
$nom = 'FicheFrais.pdf';

// Création du PDF
$pdf->Output($nom,'D');
?>