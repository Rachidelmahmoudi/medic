<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\{Consultation,ConsultationExamen,Examen,Facture,FactureConsultationExamen,Model,Patient,TypeExamen,Ville,Medecin,Origine,Mutuelle,DetailsMutuelle};
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use DateTime;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class AdminController extends AbstractController {


    /**
     * @Route("/admin",name="admin_gestion")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function index(Request $request) {

      $patients = $this->getDoctrine()->getRepository(Patient::class)->findAll();
      $medecins = $this->getDoctrine()->getRepository(Medecin::class)->findAll();
      $natures = $this->getDoctrine()->getRepository(TypeExamen::class)->findAll();
      $examens = $this->getDoctrine()->getRepository(Examen::class)->findAll();
      $mutuelles = $this->getDoctrine()->getRepository(Mutuelle::class)->findAll();
      $origines = $this->getDoctrine()->getRepository(Origine::class)->findAll();
      $villes = $this->getDoctrine()->getRepository(Ville::class)->findAll();
      $factures = $this->getDoctrine()->getRepository(Facture::class)->findAll();

      return $this->render('admin/content.html.twig',['patients'=>$patients,
            'medecins'=>$medecins,'natures'=>$natures,'examens'=>$examens,
            'mutuelles'=>$mutuelles,'origines'=>$origines,'villes'=>$villes,'factures'=>$factures]);
    }

    /**
     * @Route("/admin/api",name="admin_gestion_api")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function index_api(Request $request)
    {
        $patients = $this->getDoctrine()->getRepository(Patient::class)->findAll();
        $medecins = $this->getDoctrine()->getRepository(Medecin::class)->findAll();
        $natures = $this->getDoctrine()->getRepository(TypeExamen::class)->findAll();
        $examens = $this->getDoctrine()->getRepository(Examen::class)->findAll();
        $mutuelles = $this->getDoctrine()->getRepository(Mutuelle::class)->findAll();
        $origines = $this->getDoctrine()->getRepository(Origine::class)->findAll();
        $villes = $this->getDoctrine()->getRepository(Ville::class)->findAll();
        $factures = $this->getDoctrine()->getRepository(Facture::class)->findAll();

        return new JsonResponse(array('content' => $this->render('content/admin/content.html.twig',['patients'=>$patients,
            'medecins'=>$medecins,'natures'=>$natures,'examens'=>$examens,
            'mutuelles'=>$mutuelles,'origines'=>$origines,'villes'=>$villes,'factures'=>$factures])->getContent()));
    }


    /**
     * @Route("/admin/mutuelles/delete/{id}",name="admin_mutuelles_delete")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function mutuellesDelete(Request $request,$id) {
        try {
          $entityManager = $this->getDoctrine()->getManager();
          $mutuelle = $this->getDoctrine()->getRepository(Mutuelle::class)->find($id);
          $entityManager->remove($mutuelle);
          $entityManager->flush();
          $this->addFlash('message', 'Mutuelle été bien supprimé');
           return $this->redirectToRoute('admin_mutuelles');
        } catch (\Exception $e) {
              //$this->addFlash('message', 'Patient été bien enregistré');
              return $this->redirectToRoute('admin_mutuelles');
        }
    }


    /*************** Origine *****************/


    /**
     * @Route("/admin/origines/{id}",name="admin_origines",defaults={"id": 0})
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function origines(Request $request,$id) {

        $entityManager = $this->getDoctrine()->getManager();

      $origines = $this->getDoctrine()->getRepository(Origine::class)->findAll();
      $origine = new Origine();
      $form = $this->createForm(\App\Form\OrigineType::class);

      if($id!=0)
      {
        $origine = $this->getDoctrine()->getRepository(Origine::class)->find($id);
        $form = $this->createForm(\App\Form\OrigineType::class,$origine);
        $form->handleRequest($request);
      }


      if ($form->isSubmitted()) {
            $data = $form->getData();
            $entityManager->persist($data);
            $entityManager->flush();
            $this->addFlash('message', 'Origine été bien enregistré');

        }

      return $this->render('admin/origines.html.twig',['origines'=>$origines,'formorigine' =>$form->createView(),'menuactive'=>4]);
    }


    /**
     * @Route("/admin/origines/delete/{id}",name="admin_origines_delete")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function originesDelete(Request $request,$id) {
        try {
          $entityManager = $this->getDoctrine()->getManager();
          $origine = $this->getDoctrine()->getRepository(Origine::class)->find($id);
          $entityManager->remove($origine);
          $entityManager->flush();
          $this->addFlash('message', 'Origine été bien supprimé');
           return $this->redirectToRoute('admin_origines');
        } catch (\Exception $e) {
              //$this->addFlash('message', 'Patient été bien enregistré');
              return $this->redirectToRoute('admin_origines');
        }
    }

      /*************** Ville *****************/


    /**
     * @Route("/admin/villes/{id}",name="admin_villes",defaults={"id": 0})
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function villes(Request $request,$id) {

        $entityManager = $this->getDoctrine()->getManager();

      $villes = $this->getDoctrine()->getRepository(Ville::class)->findAll();
      $ville = new Ville();
      $form = $this->createForm(\App\Form\VilleType::class);

      if($id!=0)
      {
        $ville = $this->getDoctrine()->getRepository(Ville::class)->find($id);
        $form = $this->createForm(\App\Form\VilleType::class,$examen);
        $form->handleRequest($request);
      }


      if ($form->isSubmitted()) {
            $data = $form->getData();
            $entityManager->persist($data);
            $entityManager->flush();
            $this->addFlash('message', 'Ville été bien enregistré');

        }

      return $this->render('admin/villes.html.twig',['villes'=>$villes,'formville' =>$form->createView(),'menuactive'=>4]);
    }


    /**
     * @Route("/admin/villes/delete/{id}",name="admin_villes_delete")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function villesDelete(Request $request,$id) {
        try {
          $entityManager = $this->getDoctrine()->getManager();
          $ville = $this->getDoctrine()->getRepository(Ville::class)->find($id);
          $entityManager->remove($ville);
          $entityManager->flush();
          $this->addFlash('message', 'Ville été bien supprimé');
           return $this->redirectToRoute('admin_villes');
        } catch (\Exception $e) {
              //$this->addFlash('message', 'Patient été bien enregistré');
              return $this->redirectToRoute('admin_villes');
        }
    }

    /*************** Facture *****************/


  /**
   * @Route("/admin/factures/{id}",name="admin_factures",defaults={"id": 0})
   * @Security("is_fully_authenticated()")
   * @return Response
   */
  public function factures(Request $request,$id) {

      $entityManager = $this->getDoctrine()->getManager();

    $factures = $this->getDoctrine()->getRepository(Facture::class)->findAll();


    $facture = null;

    if($id!=0)
    {
      $facture = $this->getDoctrine()->getRepository(Facture::class)->find($id);
    }

    return $this->render('admin/factures.html.twig',['factures'=>$factures,'facture' =>$facture,'menuactive'=>4]);
  }


  /**
   * @Route("/admin/factures/delete/{id}",name="admin_factures_delete")
   * @Security("is_fully_authenticated()")
   * @return Response
   */
  public function facturesDelete(Request $request,$id) {
      try {
        $entityManager = $this->getDoctrine()->getManager();
        $facture = $this->getDoctrine()->getRepository(Facture::class)->find($id);
        $entityManager->remove($facture);
        $entityManager->flush();
        $this->addFlash('message', 'Facture été bien supprimé');
         return $this->redirectToRoute('admin_factures');
      } catch (\Exception $e) {
            //$this->addFlash('message', 'Patient été bien enregistré');
            return $this->redirectToRoute('admin_factures');
      }
  }




}
