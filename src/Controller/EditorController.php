<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 24/04/2020
 * Time: 00:08
 */

namespace App\Controller;

use App\Entity\ConsultationExamen;
use App\Entity\FactureConsultationExamen;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class EditorController extends AbstractController {

    /**
     * @Route("/etiquette/{id}", name="editor_etiquetteprestation")
     * @Security("is_fully_authenticated()")
     */
    public function etiquettePrestation($id)
    {
        $ConsultationExamen = $this->getDoctrine()->getRepository(ConsultationExamen::class)->findOneBy(['id'=>$id]);
        if(!$ConsultationExamen)
            $ConsultationExamen = [];

        return $this->render('rapport/etiquette-prestation.html.twig', ['info' => $ConsultationExamen]);
    }



    /**
     * @Route("/facture/{id}", name="editor_factureprestation")
     * @Security("is_fully_authenticated()")
     */
    public function facturePrestation($id)
    {
        $facture = $this->getDoctrine()->getRepository(FactureConsultationExamen::class)->findOneBy(['consult_examen'=>$id]);
        if($facture)
        {
            return $this->render('rapport/facture-prestation.html.twig',['facture'=>$facture]);
        }
        return $this->render('rapport/facture-prestation.html.twig',['prestation'=>$id]);
    }
    
    /**
     * @Route("/etiquette/all/{id}", name="editor_allEtiquettes")
     * @Security("is_fully_authenticated()")
     */
    public function allEtiquettes($id, Request $request)
    {
        $etiquettes = $this->getDoctrine()->getRepository(ConsultationExamen::class)->findBy(['Consultation' => $id]);

        if (!$etiquettes) {
            $etiquettes = [];
        }

        return $this->render('rapport/etiquette-dossier.html.twig', ['etiquettes' => $etiquettes]);
    }
    
    /*************** Old Code ************/
    
    
    /**
     * @Route("/RunWordDoc", name="RunWordDoc")
     * @Security("is_fully_authenticated()")
     */
    public function RunWordDoc(Request $request)
    {

        $entityManager = $this->getDoctrine()->getManager();
        $data = $request->request->all();
        $idexamain_consultat = $data['idexamin_consultation'];

        $ConsultationExamen = $this->getDoctrine()->getRepository(ConsultationExamen::class)->find($idexamain_consultat);

        if ($ConsultationExamen->getIshaverapport() == 1) {

            exec('.\create-word.bat ' . $idexamain_consultat);
            $response = 1;
            return new Response(json_encode($response));

        }

        //--------DATA PATIENT---------

        $nom = $ConsultationExamen->getConsultation()->getpatient()->getnom();
        $prenom = $ConsultationExamen->getConsultation()->getpatient()->getprenom();
        $cin = $ConsultationExamen->getConsultation()->getpatient()->getcin();
        $sexe = $ConsultationExamen->getConsultation()->getpatient()->getsexe();
        $tel = $ConsultationExamen->getConsultation()->getpatient()->gettel();
        $ville = $ConsultationExamen->getConsultation()->getpatient()->getville()->getville();
        $adresse = $ConsultationExamen->getConsultation()->getpatient()->getadresse();
        $date_naiss = $ConsultationExamen->getConsultation()->getpatient()->getdatenaiss();

        $phpWord = new PhpWord();

        /* Note: any element you append to a document must reside inside of a Section. */

        // Adding an empty Section to the document...
        $section = $phpWord->addSection();
        // Adding Text element to the Section having font styled by default...
        $section->addText('Nom : ' . $nom . ' ' . $prenom);
        $section->addText('cin : ' . $cin);
        $section->addText('sexe : ' . $sexe);
        $section->addText('tel : ' . $tel);
        $section->addText('ville : ' . $ville);

        // Saving the document as OOXML file...
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');

        $filePath = "editor/doc/" . $idexamain_consultat . '.docx';
        // Write file into path

        try {

            $objWriter->save($filePath);
            exec('.\create-word.bat ' . $idexamain_consultat);

            $ConsultationExamen->setIshaverapport(1);
            $entityManager->persist($ConsultationExamen);
            $entityManager->flush();

            $response = 1;

        } catch (\Exception $e) {
            $response = 0;

        }

        return new Response(json_encode($response));

    }
    /**
     * @Route("/updateRapport", name="updateRapport")
     */
    public function updateRapport(Request $request)
    {

        $entityManager = $this->getDoctrine()->getManager();

        $data = $request->request->all();

        $bodyData = $data['bodyData'];
        $idexamin_consultation = $data['idexamin_consultation'];

        $ConsultationExamen = $this->getDoctrine()->getRepository(ConsultationExamen::class)->find($idexamin_consultation);
        $ConsultationExamen->setrapport($bodyData);

        try {

            $entityManager->persist($ConsultationExamen);
            $entityManager->flush();
            $response = 1;

        } catch (\Exception $e) {
            $response = 0;

        }

        return new Response(json_encode($response));

    }

    /**
     * @Route("/compteRendu/{id}", name="compteRendu")
     */
    public function compteRendu($id, Request $request)
    {

        $entityManager = $this->getDoctrine()->getManager();

        $bodyTemplate = $this->getDoctrine()->getRepository(Model::class)->find(1)->getRapport();
        $ConsultationExamen = $this->getDoctrine()->getRepository(ConsultationExamen::class)->find($id);

        if ($ConsultationExamen->getIshaverapport() == 1) {
            $bodyTemplate = $ConsultationExamen->getRapport();

        }

        //--------DATA PATIENT---------

        $nom = $ConsultationExamen->getConsultation()->getpatient()->getnom();
        $prenom = $ConsultationExamen->getConsultation()->getpatient()->getprenom();
        $cin = $ConsultationExamen->getConsultation()->getpatient()->getcin();
        $sexe = $ConsultationExamen->getConsultation()->getpatient()->getsexe();
        $tel = $ConsultationExamen->getConsultation()->getpatient()->gettel();
        $ville = $ConsultationExamen->getConsultation()->getpatient()->getville()->getville();
        $adresse = $ConsultationExamen->getConsultation()->getpatient()->getadresse();
        $date_naiss = $ConsultationExamen->getConsultation()->getpatient()->getdatenaiss();

        $age = date_format($date_naiss, 'Y-m-d');

        $bodyTemplate = str_replace("updatenomprenom", $nom . ' ' . $prenom, $bodyTemplate);
        $bodyTemplate = str_replace("updatetel", $tel, $bodyTemplate);
        $bodyTemplate = str_replace("updateville", $ville, $bodyTemplate);
        $bodyTemplate = str_replace("updateadresse", $adresse, $bodyTemplate);
        $bodyTemplate = str_replace("updatecin", $cin, $bodyTemplate);
        $bodyTemplate = str_replace("updatebirthday", $age, $bodyTemplate);

        return $this->render('rapport/compteRendu.html.twig', ['idexamin_consultation' => $id, 'bodyTemplate' => $bodyTemplate, 'ishaveRapport' => $ConsultationExamen->getIshaverapport()]);
    }

    /**
     * @Route("/facture/{id}", name="facture")
     */
    public function facture($id, Request $request)
    {

        $entityManager = $this->getDoctrine()->getManager();

        // $bodyTemplate = $this->getDoctrine()->getRepository(Model::class)->find(2)->getRapport();
        $ConsultationExamen = $this->getDoctrine()->getRepository(ConsultationExamen::class)->find($id);

        //--------DATA PATIENT---------

        $nom = $ConsultationExamen->getConsultation()->getpatient()->getnom();
        $prenom = $ConsultationExamen->getConsultation()->getpatient()->getprenom();
        $cin = $ConsultationExamen->getConsultation()->getpatient()->getcin();
        $sexe = $ConsultationExamen->getConsultation()->getpatient()->getsexe();
        $tel = $ConsultationExamen->getConsultation()->getpatient()->gettel();
        $ville = $ConsultationExamen->getConsultation()->getpatient()->getville()->getville();
        $adresse = $ConsultationExamen->getConsultation()->getpatient()->getadresse();
        $date_naiss = $ConsultationExamen->getConsultation()->getpatient()->getdatenaiss();
        $examName = $ConsultationExamen->getexamen()->getnom();

        $getavance = $ConsultationExamen->getavance();
        $getprix = $ConsultationExamen->getprix();
        $getreste = $ConsultationExamen->getreste();

        $date_paiement = $ConsultationExamen->getDatePaiement();


        $date_pai = date_format($date_paiement, 'd/m/Y h:i');

        // $bodyTemplate = str_replace("updatenomprenom", $nom . ' ' . $prenom, $bodyTemplate);
        // $bodyTemplate = str_replace("updatepaye", $updatepaye, $bodyTemplate);
        // $bodyTemplate = str_replace("updatetotal", $updatepaye, $bodyTemplate);
        // $bodyTemplate = str_replace("updateavance", $updateavance, $bodyTemplate);
        // $bodyTemplate = str_replace("updatereste", $updatereste, $bodyTemplate);
        // $bodyTemplate = str_replace("updatedate", $date_pai, $bodyTemplate);

        $info['nomcomplet'] = $nom . ' ' . $prenom;
        $info['cin'] = $cin;
        $info['sexe'] = $sexe;
        $info['tel'] = $tel;
        $info['ville'] = $ville;
        $info['adresse'] = $adresse;
        $info['date_pai'] = $date_pai;
        $info['examName'] = $examName;
        $info['getprix'] = $getprix;
        $info['getreste'] = $getreste;
        $info['getavance'] = $getavance;

        return $this->render('rapport/facture.html.twig', ['info' => $info]);

    }

    /**
     * @Route("/etiquette/{id}", name="etiquette")
     */
    public function etiquette($id, Request $request)
    {

        $ConsultationExamen = $this->getDoctrine()->getRepository(ConsultationExamen::class)->findOneBy(['id'=>$id]);

        if(!$ConsultationExamen)
            $ConsultationExamen = [];
        

        return $this->render('rapport/etiquette.html.twig', ['info' => $ConsultationExamen]);
    }
}