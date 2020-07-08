<?php
/**
 * Created by PhpStorm.
 * User: Asus
 * Date: 22/04/2020
 * Time: 10:32
 */
namespace App\Controller;

use App\Entity\Examen;
use App\Entity\Mutuelle;
use App\Entity\Medecin;
use App\Entity\Origine;
use App\Entity\Patient;
use App\Entity\TypeExamen;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;




class MedicalController extends AbstractController {

    /**
     * @Route("/", name="medical_recherche")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function recherche(Request $request)
    {
        return $this->render('medical/prestations.html.twig',['menuactive'=>1]);
    }

    /**
     * @Route("/dossier-patients", name="medical_dossiers_patients")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function dossiersPatients(Request $request)
    {
        return $this->render('medical/consultations.html.twig',['menuactive'=>2]);
    }

    /**
     * @Route("/nouvelle-consultation", name="medical_nouvelle_consultation")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function nouvelleConsultation(Request $request)
    {
      $patients = $this->getDoctrine()->getRepository(Patient::class)->findAll();
      $mutuelles = $this->getDoctrine()->getRepository(Mutuelle::class)->findAll();
      $origines = $this->getDoctrine()->getRepository(Origine::class)->findAll();
      $medcins = $this->getDoctrine()->getRepository(Medecin::class)->findAll();
      $types = $this->getDoctrine()->getRepository(TypeExamen::class)->findAll();

        return $this->render('medical/nouvelle-consultation.html.twig',['patients'=>$patients,'mutuelles'=>$mutuelles,'origines'=>$origines,'medcins'=>$medcins,'types'=>$types,'menuactive'=>3]);
    }

    /**
     * @Route("/admin", name="medical_admin")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function admin(Request $request)
    {
        return $this->render('medical/index.html.twig',['menuactive'=>4]);
    }

    public function subPrestations(Request $request){
        $id = $request->get('consultation');
        $prest = $this->getDoctrine()->getRepository(Examen::class)->findByConsultation($id);
        return $this->render('content/sub-prestations.html.twig',['prest'=> $prest]);
    }

}
