<?php

namespace App\Controller;

use App\Entity\Exposition;
use App\Entity\Visite;
use Doctrine\Persistence\ManagerRegistry;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Writer\SvgWriter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreateVisiteController extends AbstractController
{
    #[Route('/', name: 'app_create_visite', methods: ['POST', 'GET'])]
    public function index(ManagerRegistry $doctrine, Request $request): Response
    {
        $jauge = 500;
        $message = null;
        $nbVisiteursEnCours = 0;
        $visitesEnCours = $doctrine->getRepository(Visite::class)->findBy(['dateHeureDepart' => null]);
        foreach ($visitesEnCours as $v) {
            $nbVisiteursEnCours += $v->getNbVisiteursAdultes() + $v->getNbVisiteursEnfants();
        }
        $expos = $doctrine->getRepository(Exposition::class)->findBy(['active' => true]);

        $visite = new Visite();
        $visite->setDateHeureArrivee(new \DateTime('now'));
        $visite->setNbVisiteursEnfants(0);
        $visite->setNbVisiteursAdultes(0);

        if ($request->get('nbEnfants')) {
            $visite->setNbVisiteursEnfants($request->get('nbEnfants'));
        }
        if ($request->get('nbAdultes')) {
            $visite->setNbVisiteursAdultes($request->get('nbAdultes'));
        }

        foreach ($expos as $expo) {
            if ($request->get($expo->getId())) {
                $visite->addExposition($expo);
            }
        }

        if ($request->get('valider') !== null) {
            if ($visite->checkAtLeastOneVisitor() === true && $visite->getExpositions()->count() != 0 && $nbVisiteursEnCours <= $jauge) {
                $doctrine->getManager()->persist($visite);
                $doctrine->getManager()->flush();

                $writer = new PngWriter();

                // Create QR code
                $qrCode = QrCode::create($visite->getId())
                    ->setEncoding(new Encoding('UTF-8'))
                    ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
                    ->setSize(300)
                    ->setMargin(1)
                    ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
                    ->setForegroundColor(new Color(0, 0, 0))
                    ->setBackgroundColor(new Color(255, 255, 255));

                // Create generic logo
                $logo = Logo::create('images/anibis-logo-share.png')
                    ->setResizeToWidth(50);

                $qrCodeResult = $writer->write($qrCode, $logo);

                return $this->render('create_visite/confirmVisite.html.twig', [
                    'visite' => $visite,
                    'qrCode' => $qrCodeResult,
                ]);
            } elseif (!$visite->checkAtLeastOneVisitor()) {
                $message = 'Vous devez ajouter au moins un adulte ou enfant';
            } elseif ($visite->getExpositions()->count() == 0) {
                $message = 'Vous devez cocher au moins une exposition';
            } else {
                $message = 'Jauge dépassée';
            }
        }

        return $this->render('create_visite/index.html.twig', [
            'expos' => $expos,
            'visite' => $visite,
            'message' => $message,
         ]);
    }
}
