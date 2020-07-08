<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Consultation;
use App\Entity\ConsultationExamen;
use App\Entity\Examen;
use App\Entity\Facture;
use App\Entity\FactureConsultationExamen;
use App\Entity\Model;
use App\Entity\Patient;
use App\Entity\TypeExamen;
use App\Entity\Ville;
use App\Entity\Medecin;
use App\Entity\Origine;
use App\Entity\Mutuelle;
use App\Entity\DetailsMutuelle;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use DateTime;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ApiController extends AbstractController
{

    /**
     * @Route("/api/prestations",name="api_prestations_list")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function api_prestations(Request $request)
    {
        $d1 = null;
        $d2 = null;
        if (count($request->request->all()) > 0) {
            $d1 = date('Y-m-d', strtotime($request->get('date1')));
            $d2 = date('Y-m-d', strtotime($request->get('date2')));

            $prestations = $this->getDoctrine()->getRepository(ConsultationExamen::class)->findTodayOrBetween($d1, $d2);
        } else {
            $prestations = $this->getDoctrine()->getRepository(ConsultationExamen::class)->findTodayOrBetween($d1, $d2);
        }


        return new JsonResponse(array('content' => $this->render('content/prestations.html.twig', [
            'prestations' => $prestations])->getContent()));
    }

    /**
     * @Route("/nature/examens/get",name="api_examens_by_nature")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function api_examensGet(Request $request)
    {
        $nature = $request->get('nature');
        $examens = $this->getDoctrine()->getRepository(Examen::class)->findBy(['type' => $nature]);

        return new JsonResponse(array('data' => $this->jsonSerialiser($examens)));
    }

    public function jsonSerialiser($data)
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $data = $serializer->serialize($data, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return json_decode($data, true);
    }

    /**
     * @Route("/examens/prix/get",name="api_prix_by_examen")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function api_prixGet(Request $request)
    {
        $examen = $request->get('examen');
        $exm = $this->getDoctrine()->getRepository(Examen::class)->find($examen);

        if ($exm) {
            return new JsonResponse(array('prix' => $exm->getPrix()));
        }

        return new JsonResponse(array('prix' => 0));
    }

    /**
     * @Route("/api/consultations",name="api_consultations_list")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function api_consultations(Request $request)
    {
        $d1 = null;
        $d2 = null;
        if (count($request->request->all()) > 0) {
            $d1 = date('Y-m-d', strtotime($request->get('date1')));
            $d2 = date('Y-m-d', strtotime($request->get('date2')));

            $consultations = $this->getDoctrine()->getRepository(Consultation::class)->findTodayOrBetween($d1, $d2);
        } else {
            $consultations = $this->getDoctrine()->getRepository(Consultation::class)->findTodayOrBetween($d1, $d2);
        }


        return new JsonResponse(array('content' => $this->render('content/consultations.html.twig', [
            'consultations' => $consultations])->getContent()));
    }

    /**
     * @Route("/api/consultations/add",name="api_consultations_add")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function api_ConsultationAdd(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        // echo "<pre>";
        // print_r($request->request->all());
        // exit;
        try {
            $data = $request->request->all();
            //****************** get the patient **********/
            $patient = new Patient();
            if (isset($data['patientid'])) {
                $patient = $this->getDoctrine()->getRepository(Patient::class)->find($data['patientid']);
            } else {
                $p = $data['patient'];
                if (isset($p['nom'])) {
                    $patient->setNom($p['nom']);
                }
                if (isset($p['cin'])) {
                    $patient->setPrenom($p['cin']);
                }
                if (isset($p['cin'])) {
                    $patient->setCin($p['cin']);
                }
                if (isset($p['adresse'])) {
                    $patient->setAdresse($p['adresse']);
                }
                if (isset($p['sexe'])) {
                    $patient->setSexe($p['sexe']);
                }
                if (isset($p['tel'])) {
                    $patient->setTel($p['tel']);
                }
                if (isset($p['date_naiss'])) {
                    $patient->setDateNaiss(new \DateTime($p['date_naiss']));
                }
                if (isset($p['situation'])) {
                    $patient->setSituation($p['situation']);
                }
                if (isset($p['ville'])) {
                    $ville = $this->getDoctrine()->getRepository(Ville::class)->find($p['ville']);
                    $patient->setVille($ville);
                }
                if (isset($p['mutuelle'])) {
                    $mutuelle = $this->getDoctrine()->getRepository(Mutuelle::class)->find($p['mutuelle']);
                    $patient->setMutuelle($mutuelle);
                }

                $entityManager->persist($patient);
            }

            //****************** get the mutuelle **********/
            $mututelle = null;

            if ($data['mutualiste'] != 0) {
                $mutuelle = $this->getDoctrine()->getRepository(Mutuelle::class)->find($data['mutualiste']);
                if ($data['parente'] != -1) {
                    $detailsmutuelle = new  DetailsMutuelle();

                    $detailsmutuelle->setParente($data['parente']);

                    if (isset($data['nomadh']) && !empty($data['nomadh'])) {
                        $detailsmutuelle->setNomAdh($data['nomadh']);
                    }

                    if (isset($data['prenomadh']) && !empty($data['prenomadh'])) {
                        $detailsmutuelle->setPrenomAdh($data['prenomadh']);
                    }

                    if (isset($data['cinadh']) && !empty($data['cinadh'])) {
                        $detailsmutuelle->setCinAdh($data['cinadh']);
                    }

                    if (isset($data['nMutu']) && !empty($data['nMutu'])) {
                        $detailsmutuelle->setNMutuelle($data['nMutu']);
                    }

                    $detailsmutuelle->setMutuelle($mutuelle);

                    $entityManager->persist($detailsmutuelle);
                }
            }

            //****************** get the medecin **********/
            $medecin = new Medecin();
            $origine = null;
            if ($data['origine'] != 0) {
                $origine = $this->getDoctrine()->getRepository(Origine::class)->find($data['origine']);
            }

            if ($data['docteur'] != -1) {
                $medecin = $this->getDoctrine()->getRepository(Medecin::class)->find($data['docteur']);
            } else {
                if (isset($data['nommedecin']) && !empty($data['nommedecin'])) {
                    $medecin->setNom($data['nommedecin']);
                }

                if (isset($data['prenommedecin']) && !empty($data['prenommedecin'])) {
                    $medecin->setPrenom($data['prenommedecin']);
                }

                $entityManager->persist($medecin);
            }

            //****************** get the examen  **********/

            $consultationexamen = new ConsultationExamen();
            $consultation = new Consultation();

            if (isset($data['statut-consultation']) && !empty($data['statut-consultation'])) {
                $consultation->setEtat($data['statut-consultation']);
                $consultationexamen->setEtat($data['statut-consultation']);
            }

            //echo $data['date-consultation'];exit;

            if (isset($data['date-consultation']) && !empty($data['date-consultation'])) {
                $consultation->setDateConsultation(new \DateTime($data['date-consultation']));
            }

            if (isset($data['presentation']) && !empty($data['presentation'])) {
                $examen = $this->getDoctrine()->getRepository(Examen::class)->find($data['presentation']);
                $consultationexamen->setExamen($examen);
            }

            if (isset($data['prix']) && !empty($data['prix'])) {
                $consultation->setPrixTotal($data['prix']);
                $consultationexamen->setPrix($data['prix']);
            }

            if (isset($data['avance']) && !empty($data['avance'])) {
                $consultationexamen->setAvance($data['avance']);
                $consultation->setAvance($data['avance']);
            }

            if (isset($data['statutpaiement']) && !empty($data['statutpaiement'])) {
                $consultation->setStatut($data['statutpaiement']);
                $consultationexamen->setStatut($data['statutpaiement']);
            }

            if (isset($data['date-resultat']) && !empty($data['date-resultat'])) {
                $consultationexamen->setDateResultat(new \DateTime($data['date-resultat']));
            }

            if (isset($data['personneacontacter']) && !empty($data['personneacontacter'])) {
                $consultation->setPersonneAContacter($data['personneacontacter']);
            }

            if (isset($data['adressecontact']) && !empty($data['adressecontact'])) {
                $consultation->setAdresseContact($data['adressecontact']);
            }

            if (isset($data['telephone']) && !empty($data['telephone'])) {
                $consultation->setTelContact($data['telephone']);
            }

            if (isset($data['patient']['rc']) && !empty($data['patient']['rc'])) {
                $consultation->setRClinique($data['patient']['rc']);
            }

            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $consultation->setUser($user);

            $consultationexamen->setConsultation($consultation);

            $consultation->setMedecin($medecin);

            $consultation->setOrigine($origine);

            $consultation->setPatient($patient);

            $entityManager->persist($consultation);

            $entityManager->persist($consultationexamen);

            $entityManager->flush();

            return new JsonResponse(['success' => true, 'message' => "La consultation est enrgistrée"], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => 'Erreur => ' . $e->getMessage()], Response::HTTP_OK);
        }
    }

    /**
     * @Route("/api/consultations/add-prestation",name="api_consultations_add_prestation")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function api_ConsultationAddPrestation(Request $request)
    {
        $data = $request->get('dataS');
        $entityManager = $this->getDoctrine()->getManager();

        $consultation = $data['consultation'];
        $examen = $data['examen'];
        $prix = $data['prix'];
        $regelement = $data['regelement'];
        $etat = $data['etat'];

        try {
            $lExamen = $this->getDoctrine()->getRepository(Examen::class)->find($examen);
            if (!$lExamen) {
                return new JsonResponse(['success' => false, 'message' => 'Examen non trouvé'], Response::HTTP_OK);
            }

            $lConsultation = $this->getDoctrine()->getRepository(Consultation::class)->find($consultation);
            if (!$lConsultation) {
                return new JsonResponse(['success' => false, 'message' => 'Consultation non trouvé'], Response::HTTP_OK);
            }

            $lce = $this->getDoctrine()->getRepository(ConsultationExamen::class)->findBy(['examen' => $examen, 'Consultation' => $consultation]);

            if (!$lce) {
                $ce = new ConsultationExamen();

                $ce->setExamen($lExamen);
                $ce->setConsultation($lConsultation);
                $ce->setStatut($regelement);


                $ce->setPrix($prix);
                $ce->setReste(0);
                $ce->setAvance(0);
                $ce->setEtat($etat);

                $lConsultation->setPrixTotal(floatval($lConsultation->getPrixTotal()) + $prix);
                $entityManager->persist($lConsultation);
                $entityManager->flush();


                $entityManager->persist($ce);
                $entityManager->flush();

                $encoders = [new JsonEncoder()]; // If no need for XmlEncoder
                $normalizers = [new ObjectNormalizer()];
                $serializer = new Serializer($normalizers, $encoders);

                // Serialize your object in Json
                $jsonObject = $serializer->serialize($ce, 'json', [
                    'circular_reference_handler' => function ($object) {
                        return new JsonResponse(['success' => true, 'message' => 'Tout est enregistré'], Response::HTTP_OK);
                    },
                ]);


                return new JsonResponse(['success' => true, 'message' => 'Tout est enregistré'], Response::HTTP_OK);
            }

            return new JsonResponse(['success' => false, 'message' => 'Deja affecté'], Response::HTTP_OK);
        } catch (Exception $ex) {
            return new JsonResponse(['success' => false, 'message' => 'Erreur interne ' . $ex->getMessage()], Response::HTTP_OK);
        }
    }

    /**
     * @Route("/api/prestation/change_state",name="api_prestations_change_state")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function api_prestationChangeState(Request $request)
    {
        $id = $request->get('id');
        $etat = $request->get('etat');
        $type = $request->get('type');
        if (!is_null($id) && !is_null($etat)) {
            try {
                $ce = $this->getDoctrine()->getRepository(ConsultationExamen::class)->find($id);
                if (!$ce) {
                    return new JsonResponse(['success' => false], Response::HTTP_OK);
                }

                $ce->setEtat($etat);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($ce);
                $entityManager->flush();

                return new JsonResponse(['success' => true], Response::HTTP_OK);
            } catch (Exception $ex) {
                return new JsonResponse(['success' => false], Response::HTTP_OK);
            }
        }
    }

    /**
     * @Route("/api/consultation/change_state",name="api_consultations_change_state")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function api_consultationChangeState(Request $request)
    {
        $id = $request->get('id');
        $etat = $request->get('etat');
        $type = $request->get('type');
        if (!is_null($id) && !is_null($etat)) {
            try {
                $c = $this->getDoctrine()->getRepository(Consultation::class)->find($id);
                if (!$c) {
                    return new JsonResponse(['success' => false], Response::HTTP_OK);
                }

                $c->setEtat($etat);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($c);
                $entityManager->flush();

                return new JsonResponse(['success' => true], Response::HTTP_OK);
            } catch (Exception $ex) {
                return new JsonResponse(['success' => false], Response::HTTP_OK);
            }
        }
    }

    /**
     * @Route("/api/prestation/get",name="api_prestations_get")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function api_prestationGet(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $id = $request->get('p');
        $tab = $request->get('tab');

        $bodyTemplate = null;

        /** * Paiement Tab ***** */
        $paiement = $entityManager->getRepository(ConsultationExamen::class)->find($id);

        $id_consultation = $paiement->getConsultation()->getId();

        $temp = $this->getDoctrine()->getRepository(Model::class)->find($id);
        if ($temp) {
            $bodyTemplate = $temp->getRapport();
        }

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


        return new JsonResponse(array('content' => $this->render('content/prestation-details.html.twig', [
            'prestation' => $id,
            'paiement' => $paiement,
            'tab' => $tab,
            'consultation' => $id_consultation,
            'bodyTemplate' => $bodyTemplate, 'ishaveRapport' => $ConsultationExamen->getIshaverapport(),
        ])->getContent()));
    }

    /**
     * @Route("/api/consultation/get",name="api_consultations_get")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function api_consultationGet(Request $request)
    {
        $id = $request->get('c');
        $tab = $request->get('tab');

        $entityManager = $this->getDoctrine()->getManager();

        $consultation = $entityManager->getRepository(Consultation::class)
            ->find($id);

        if (!$consultation) {
            return new JsonResponse(array('content' => '<div class="alert alert-danger alert-dismissible" role="alert">
                                                      <div class="alert-message">
                                                          <strong>Erreur !</strong> consultation non trouvé
                                                      </div>

                                                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                          <span aria-hidden="true">×</span>
                                                      </button>
									                </div>'));
        }


        $rc = $consultation->getRClinique();

        $p = $consultation->getPatient();

        $facturesgenere = $entityManager->getRepository(FactureConsultationExamen::class)->findFacturesgeneres($id);

        $facturesnogenere = $entityManager->getRepository(ConsultationExamen::class)->findFacturesnogeneres($id);

        $prestations = $entityManager->getRepository(ConsultationExamen::class)
            ->findByConsultation($id);

        if (!$p) {
            return new JsonResponse(array('content' => '<div class="alert alert-danger alert-dismissible" role="alert">
                                                      <div class="alert-message">
                                                          <strong>Erreur !</strong> patient non trouvé
                                                      </div>
                                                      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                          <span aria-hidden="true">×</span>
                                                      </button>
									                </div>'));
        }


        return new JsonResponse(array('content' => $this->render('content/consultation-details.html.twig', [
            'id' => $id,
            'rc' => $rc,
            'tab' => $tab,
            'patient' => $p->getId(),
            'facturesgenere' => $facturesgenere,
            'facturesnogenere' => $facturesnogenere,
            'prestations' => $prestations,
            'allTypes' => $entityManager->getRepository(TypeExamen::class)->findAll(),
        ])->getContent()));
    }

    /**
     * @Route("/api/prestation/pay",name="api_prestations_pay")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function api_prestationPay(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $avance = $request->get('avance');
        $reste = $request->get('reste');
        $id = $request->get('id');
        $total = $request->get('total');

        try {
            $etatpaiement = $reste == 0 ? 2 : (($avance < $total && $avance != 0) ? 1 : 0);
            $ce = $this->getDoctrine()->getRepository(ConsultationExamen::class)->find($id);
            $idcon = $ce->getConsultation()->getId();
            $ce->setAvance($avance);
            $ce->setStatut($etatpaiement);
            $ce->setReste($reste);
            $entityManager->persist($ce);
            $entityManager->flush();

            $ces = $this->getDoctrine()->getRepository(ConsultationExamen::class)->findBy(['Consultation' => $idcon]);

            $avance = 0;
            $reste = 0;
            $total = 0;

            foreach ($ces as $c) {
                $reste += floatval($c->getReste());
                $avance += floatval($c->getAvance());
                $total += floatval($c->getPrix());
            }

            $c = $this->getDoctrine()->getRepository(Consultation::class)->find($idcon);

            $c->setPrixTotal($total);
            $c->setAvance($avance);

            $etatpaiement = $reste == 0 ? 2 : (($avance < $total && $avance != 0) ? 1 : 0);
            $c->setStatut($etatpaiement);

            $entityManager->persist($c);
            $entityManager->flush();

            return new JsonResponse(['success' => true], Response::HTTP_OK);
        } catch (Exception $ex) {
            return new JsonResponse(['success' => false], Response::HTTP_OK);
        }
    }

    /**
     * @Route("/api/prestations/consultation",name="api_prestations_de_consultation")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function api_prestationsDeConsultation(Request $request)
    {
        $consult = $request->get('id');
        $tbody = $request->get('tbody');
        $prestations = $this->getDoctrine()->getRepository(ConsultationExamen::class)->findByConsultation($consult);

        if (is_null($prestations)) {
            return new JsonResponse(['success' => false], Response::HTTP_OK);
        }


        return new JsonResponse(array('content' => $this->render('content/sub-prestations-details.html.twig', [
            'prestations' => $prestations, 'id' => $consult, 'tbody' => $tbody,
        ])->getContent()));
    }

    /**
     * @Route("/api/prestations/delete",name="api_delete_consultation_examen")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function api_deleteConsultationExamen(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $id = $request->get('id');

        if (is_null($id)) {
            return new JsonResponse(['success' => false, 'message' => 'ID non valide'], Response::HTTP_OK);
        }

        try {
            $ce = $this->getDoctrine()->getRepository(ConsultationExamen::class)->find($id);
            if (!$ce) {
                if (is_null($id)) {
                    return new JsonResponse(['success' => false, 'message' => 'Prestation non trouvée'], Response::HTTP_OK);
                }
            }

            $idcons = $ce->getConsultation()->getId();
            $entityManager->remove($ce);
            $entityManager->flush();
            return new JsonResponse(['success' => true, 'message' => 'Prestation bien desaffecté'], Response::HTTP_OK);
        } catch (Exception $ex) {
            return new JsonResponse(['success' => false, 'message' => 'Erreur interne. ' . $ex->getMessage()], Response::HTTP_OK);
        }
    }

    /**
     * @Route("/api/consultation/pay",name="api_pay_dossier")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function apiPaiementDossier(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $avance = $request->get('avance');
        $reste = $request->get('reste');
        $id = $request->get('id');
        $idcon = $request->get('idcon');
        $total = $request->get('total');

        try {
            $etatpaiement = $reste == 0 ? 2 : (($avance < $total && $avance != 0) ? 1 : 0);
            $ce = $this->getDoctrine()->getRepository(ConsultationExamen::class)->find($id);
            $ce->setAvance($avance);
            $ce->setStatut($etatpaiement);
            $ce->setReste($reste);
            $entityManager->persist($ce);
            $entityManager->flush();

            $ces = $this->getDoctrine()->getRepository(ConsultationExamen::class)->findBy(['Consultation' => $idcon]);

            $avance = 0;
            $reste = 0;
            $total = 0;

            foreach ($ces as $c) {
                $reste += floatval($c->getReste());
                $avance += floatval($c->getAvance());
                $total += floatval($c->getPrix());
            }

            $c = $this->getDoctrine()->getRepository(Consultation::class)->find($idcon);

            $c->setPrixTotal($total);
            //$c->getReste($reste);
            $c->setAvance($avance);

            $etatpaiement = $reste == 0 ? 2 : (($avance < $total && $avance != 0) ? 1 : 0);
            $c->setStatut($etatpaiement);

            $entityManager->persist($c);
            $entityManager->flush();

            return new JsonResponse(['success' => true, 'message' => 'Tout est bien enregistré'], Response::HTTP_OK);
        } catch (Exception $ex) {
            return new JsonResponse(['success' => false, 'message' => 'Erreur interne. ' . $ex->getMessage()], Response::HTTP_OK);
        }
    }

    /**
     * @Route("/api/patients/all",name="api_all_patients")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function apiGetPatients(Request $request)
    {
        $patients = $this->getDoctrine()->getRepository(Patient::class)->findAll();
        return new JsonResponse(['success' => true, 'data' => $this->jsonSerialiser($patients)], Response::HTTP_OK);
    }

    /**
     * @Route("/api/patient/save",name="api_save_patient")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function apiSavePatient(Request $request)
    {
        $patientData = $request->get('dataS');

        $id = isset($patientData['id']) && !empty($patientData['id']) ? $patientData['id'] : null;
        $nom = isset($patientData['nom']) && !empty($patientData['nom']) ? $patientData['nom'] : null;
        $prenom = isset($patientData['prenom']) && !empty($patientData['prenom']) ? $patientData['prenom'] : null;
        $cin = isset($patientData['cin']) && !empty($patientData['cin']) ? $patientData['cin'] : null;
        $adresse = isset($patientData['adresse']) && !empty($patientData['adresse']) ? $patientData['adresse'] : null;
        $sexe = isset($patientData['sexe']) && !empty($patientData['sexe']) ? $patientData['sexe'] : null;
        $ville = isset($patientData['ville']) && !empty($patientData['ville']) ? $patientData['ville'] : null;
        $tel = isset($patientData['tel']) && !empty($patientData['tel']) ? $patientData['tel'] : null;
        $mutuelle = isset($patientData['mutuelle']) && !empty($patientData['mutuelle']) ? $patientData['mutuelle'] : null;
        $date_naiss = isset($patientData['date_naiss']) && !empty($patientData['date_naiss']) ? $patientData['date_naiss'] : null;
        $situation = isset($patientData['situation']) && !empty($patientData['situation']) ? $patientData['situation'] : null;
        $date_resultat = isset($patientData['date_resultat']) && !empty($patientData['date_resultat']) ? $patientData['date_resultat'] : null;
        $rc = isset($patientData['rc']) && !empty($patientData['rc']) ? $patientData['rc'] : null;

        $entityManager = $this->getDoctrine()->getManager();
        $p = new Patient();

        try {
            $v = $this->getDoctrine()->getRepository(Ville::class)->find($ville);
            if (!$v) {
                return new JsonResponse(['success' => false, 'message' => 'Ville non trouvée'], Response::HTTP_OK);
            }

            $m = $this->getDoctrine()->getRepository(Mutuelle::class)->find($mutuelle);
            if (!$m) {
                return new JsonResponse(['success' => false, 'message' => 'Mutuelle non trouvée'], Response::HTTP_OK);
            }

            if (!is_null($id)) {
                $p = $this->getDoctrine()->getRepository(Patient::class)->find($id);
                if (!$p) {
                    return new JsonResponse(['success' => false, 'message' => 'Patient non trouvé'], Response::HTTP_OK);
                }
                $p->setNom($nom)
                    ->setPrenom($prenom)
                    ->setCin($cin)
                    ->setAdresse($adresse)
                    ->setSexe($sexe)
                    ->setVille($v)
                    ->setTel($tel)
                    ->setMutuelle($m)
                    ->setDateNaiss(new \DateTime($date_naiss))
                    ->setSituation($situation);

                $entityManager->persist($p);
                $entityManager->flush();

                return new JsonResponse(['success' => true, 'message' => 'Tout est enregistrée', 'options' => ['id' => $p->getId()]], Response::HTTP_OK);
            } else {
                $p->setNom($nom)
                    ->setPrenom($prenom)
                    ->setCin($cin)
                    ->setAdresse($adresse)
                    ->setSexe($sexe)
                    ->setVille($v)
                    ->setTel($tel)
                    ->setMutuelle($m)
                    ->setDateNaiss(new \DateTime($date_naiss))
                    ->setSituation($situation);

                $entityManager->persist($p);
                $entityManager->flush();
                return new JsonResponse(['success' => true, 'message' => 'Tout est enregistrée', 'options' => ['id' => $p->getId()]], Response::HTTP_OK);
            }
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => $e->getMessage()], Response::HTTP_OK);
        }
    }

    /**
     * @Route("/api/patient/getForm",name="api_get_form_patient")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function formGetPatient(Request $request)
    {
        $form = $this->formPatient($request);
        $success = true;
        if ($form instanceof Response) {
            $form = $form->getContent();
        } else {
            $form = "";
            $success = false;
        }
        return new JsonResponse(['success' => $success, 'content' => $form], Response::HTTP_OK);
    }


    /**
     * @Route("/api/patient/form",name="api_form_patient")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function formPatient(Request $request)
    {
        $id = $request->get('id');
        if (!$id && $request->get('patient')) {
            $id = $request->get('patient')['id'];
        }
        $patient = new Patient();
        if (!isset($id) || is_null($id)) {
            $id = 0;
        }
        $patient = $this->getDoctrine()->getRepository(Patient::class)->find($id);
        $form = $this->createForm(\App\Form\PatientType::class);
        $rc = "";

        $form = $this->createForm(\App\Form\PatientType::class, $patient);
        if ($patient) {
            $birthDate = explode('-', ($patient->getDateNaiss())->format('m-d-Y'));
            $age = date_diff($patient->getDateNaiss(), date_create('today'))->y;
            $form->get('age')->setData($age);
            $rc = $this->getDoctrine()->getRepository(Consultation::class)->findOneBy(['patient' => $patient]) ? $this->getDoctrine()->getRepository(Consultation::class)->findOneBy(['patient' => $patient])->getRClinique() : "";
            $form->get('rc')->setData($rc);
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $data = $form->getData();
            $entityManager->persist($data);
            $entityManager->flush();
        }

        return $this->render('content/patient.html.twig', ['formpatient' => $form->createView()]);
    }


    /**
     * @Route("/admin/delete",name="admin_form_delete")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function formDelete(Request $request)
    {
        $id = $request->get('id');
        $type = $request->get('type');
        $message = null;
        $entityManager = $this->getDoctrine()->getManager();
        $object = null;

        switch ($type) {

            case 'p':
                $object = $this->getDoctrine()->getRepository(Patient::class)->find($id);
                break;

            case 'm':
                $object = $this->getDoctrine()->getRepository(Medecin::class)->find($id);
                break;

            case 'n':
                $object = $this->getDoctrine()->getRepository(TypeExamen::class)->find($id);
                break;

            case 'e':
                $object = $this->getDoctrine()->getRepository(Examen::class)->find($id);
                break;

            case 'mu':
                $object = $this->getDoctrine()->getRepository(Mutuelle::class)->find($id);
                break;

            case 'o':
                $object = $this->getDoctrine()->getRepository(Origine::class)->find($id);
                break;

            case 'v':
                $object = $this->getDoctrine()->getRepository(Ville::class)->find($id);
                break;

            default:
                break;

        }

        try {
            $entityManager->remove($object);
            $entityManager->flush();
            return new JsonResponse(array('success' => true, 'message' => 'Bien supprmié'));
        } catch (\Exception $exception) {
            return new JsonResponse(array('success' => false, 'message' => $exception->getMessage()));
        }
    }


    /**
     * @Route("/admin/update/get",name="admin_form_get")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function formGet(Request $request)
    {
        $id = $request->get('id');
        $type = $request->get('type');
        $form = null;
        $message = null;
        $action = $this->generateUrl('admin_form_update');
        if ($id == 0) {
            $action = $this->generateUrl('admin_form_add');
        }

        switch ($type) {

            case 'p':
                $p = $this->getDoctrine()->getRepository(Patient::class)->find($id) ? $this->getDoctrine()->getRepository(Patient::class)->find($id) : new Patient();

                $form = $this->createFormBuilder($p, [
                    'method' => 'POST',
                    'action' => $action
                ])
                    ->add('class', HiddenType::class, ['data' => 'p', 'mapped' => false])
                    ->add('get', HiddenType::class, ['data' => $id, 'mapped' => false])
                    ->add('nom', TextType::class, ['required' => true])
                    ->add('prenom', TextType::class, ['required' => true])
                    ->add('cin', TextType::class, ['required' => true])
                    ->add('adresse', TextType::class, ['required' => true])
                    ->add(
                        'sexe',
                        ChoiceType::class,
                        [
                            'choices' => [
                                'Masculin' => "Masculin",
                                'Féminin' => "Féminin"],
                            'required' => true
                        ]
                    )
                    ->add(
                        'situation',
                        ChoiceType::class,
                        [
                            'choices' => [
                                'Marié' => "Marié",
                                'Célibataire' => "Célibataire"]
                        ]
                    )
                    ->add('ville', EntityType::class, [
                        'class' => Ville::class,
                        'choice_label' => 'ville',
                    ])
                    ->add('tel', TextType::class)
                    ->add('mutuelle', EntityType::class, [
                        'class' => Mutuelle::class,
                        'choice_label' => 'nom',
                    ])
                    ->add('date_naiss', DateTimeType::class, ['widget' => 'single_text', 'html5' => false, 'required' => false])
                    ->add('save', SubmitType::class, ['label' => 'Valider'])
                    ->getForm();

                break;

            case 'm':
                $m = $this->getDoctrine()->getRepository(Medecin::class)->find($id) ? $this->getDoctrine()->getRepository(Medecin::class)->find($id) : new Medecin();
                $form = $this->createFormBuilder($m, [
                    'method' => 'POST',
                    'action' => $action
                ])
                    ->add('class', HiddenType::class, ['data' => 'm', 'mapped' => false])
                    ->add('get', HiddenType::class, ['data' => $id, 'mapped' => false])
                    ->add('nom', TextType::class, ['required' => true])
                    ->add('prenom', TextType::class, ['required' => true])
                    ->add('tel', TextType::class, ['required' => true])
                    ->add('email', TextType::class, ['required' => true])
                    ->add('specialite', TextType::class, ['required' => true])
                    ->add('save', SubmitType::class, ['label' => 'Valider'])
                    ->getForm();
                break;

            case 'n':
                $n = $this->getDoctrine()->getRepository(TypeExamen::class)->find($id) ? $this->getDoctrine()->getRepository(TypeExamen::class)->find($id) : new TypeExamen();
                $form = $this->createFormBuilder($n, [
                    'method' => 'POST',
                    'action' => $action
                ])
                    ->add('class', HiddenType::class, ['data' => 'n', 'mapped' => false])
                    ->add('get', HiddenType::class, ['data' => $id, 'mapped' => false])
                    ->add('nom', TextType::class, ['required' => true])
                    ->add('save', SubmitType::class, ['label' => 'Valider'])
                    ->getForm();
                break;

            case 'e':
                $e = $this->getDoctrine()->getRepository(Examen::class)->find($id) ? $this->getDoctrine()->getRepository(Examen::class)->find($id) : new Examen();
                $form = $this->createFormBuilder($e, [
                    'method' => 'POST',
                    'action' => $action
                ])
                    ->add('class', HiddenType::class, ['data' => 'e', 'mapped' => false])
                    ->add('get', HiddenType::class, ['data' => $id, 'mapped' => false])
                    ->add('nom', TextType::class, ['required' => true])
                    ->add('prix', TextType::class, ['required' => true])
                    ->add('type', EntityType::class, [
                        'class' => TypeExamen::class,
                        'choice_label' => 'nom',
                    ])
                    ->add('save', SubmitType::class, ['label' => 'Valider'])
                    ->getForm();
                break;

            case 'mu':
                $mu = $this->getDoctrine()->getRepository(Mutuelle::class)->find($id) ? $this->getDoctrine()->getRepository(Mutuelle::class)->find($id) : new Mutuelle();
                $form = $this->createFormBuilder($mu, [
                    'method' => 'POST',
                    'action' => $action
                ])
                    ->add('class', HiddenType::class, ['data' => 'mu', 'mapped' => false])
                    ->add('get', HiddenType::class, ['data' => $id, 'mapped' => false])
                    ->add('nom', TextType::class, ['required' => true])
                    ->add('save', SubmitType::class, ['label' => 'Valider'])
                    ->getForm();
                break;

            case 'o':
                $o = $this->getDoctrine()->getRepository(Origine::class)->find($id) ? $this->getDoctrine()->getRepository(Origine::class)->find($id) : new Origine();
                $form = $this->createFormBuilder($o, [
                    'method' => 'POST',
                    'action' => $action
                ])
                    ->add('class', HiddenType::class, ['data' => 'o', 'mapped' => false])
                    ->add('get', HiddenType::class, ['data' => $id, 'mapped' => false])
                    ->add('origine', TextType::class, ['required' => true])
                    ->add('save', SubmitType::class, ['label' => 'Valider'])
                    ->getForm();
                break;

            case 'v':
                $v = $this->getDoctrine()->getRepository(Ville::class)->find($id) ? $this->getDoctrine()->getRepository(Ville::class)->find($id) : new Ville();
                $form = $this->createFormBuilder($v, [
                    'method' => 'POST',
                    'action' => $action
                ])
                    ->add('class', HiddenType::class, ['data' => 'v', 'mapped' => false])
                    ->add('get', HiddenType::class, ['data' => $id, 'mapped' => false])
                    ->add('ville', TextType::class, ['required' => true])
                    ->add('save', SubmitType::class, ['label' => 'Valider'])
                    ->getForm();
                break;

            default:
                break;

        }

        return new JsonResponse(array('content' => $this->render('content/admin/formUpdate.html.twig', [
            'form' => $form->createView(), "message" => $message])->getContent()));
    }


    /**
     * @Route("/admin/add",name="admin_form_add")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function formAdd(Request $request)
    {
        $form = $request->request->all()['form'];

        $type = $form['class'];

        $entityManager = $this->getDoctrine()->getManager();

        $object = null;
        $status = false;
        $message = '';

        switch ($type) {

            case 'p':
                try {
                    $nom = $form['nom'];
                    $prenom = $form['prenom'];
                    $cin = $form['cin'];
                    $adresse = $form['adresse'];
                    $sexe = $form['sexe'];
                    $situation = $form['situation'];
                    $tel = $form['tel'];
                    $date_naiss = $form['date_naiss'];
                    $ville = $form['ville'];
                    $mutuelle = $form['mutuelle'];

                    $lVille = $this->getDoctrine()->getRepository(Ville::class)->find($ville);
                    $lMutuelle = $this->getDoctrine()->getRepository(Mutuelle::class)->find($mutuelle);

                    $p = new Patient();

                    $p->setNom($nom)
                        ->setPrenom($prenom)
                        ->setAdresse($adresse)
                        ->setCin($cin)
                        ->setDateNaiss(new DateTime($date_naiss))
                        ->setSexe($sexe)
                        ->setSituation($situation)
                        ->setVille($lVille)
                        ->setMutuelle($lMutuelle)
                        ->setTel($tel);

                    $object = $p;
                    $status = true;
                    $message = 'Patient bien rajoutée ';
                } catch (\Exception $ex) {
                    $status = false;
                    $message = $ex->getMessage();
                }

                break;

            case 'm':

                try {
                    $nom = $form['nom'];
                    $prenom = $form['prenom'];
                    $tel = $form['tel'];
                    $email = $form['email'];
                    $specialite = $form['specialite'];

                    $m = new Medecin();

                    $m->setTel($tel)
                        ->setPrenom($prenom)
                        ->setNom($nom)
                        ->setEmail($email)
                        ->setSpecialite($specialite);

                    $object = $m;
                    $status = true;
                    $message = 'Medecin bien rajoutée ';
                } catch (\Exception $ex) {
                    $status = false;
                    $message = $ex->getMessage();
                }
                break;

            case 'n':

                try {
                    $nom = $form['nom'];

                    $n = new TypeExamen();

                    $n->setNom($nom);

                    $object = $n;
                    $status = true;
                    $message = 'Nature bien rajoutée ';
                } catch (\Exception $ex) {
                    $status = false;
                    $message = $ex->getMessage();
                }

                break;

            case 'e':

                try {
                    $nom = $form['nom'];
                    $prix = $form['prix'];
                    $type = $this->getDoctrine()->getRepository(TypeExamen::class)->find($form['type']);

                    $e = new Examen();

                    $e->setNom($nom)
                        ->setPrix($prix)
                        ->setType($type);

                    $object = $e;
                    $status = true;
                    $message = 'Examen bien rajoutée ';
                } catch (\Exception $ex) {
                    $status = false;
                    $message = $ex->getMessage();
                }

                break;

            case 'mu':

                try {
                    $nom = $form['nom'];

                    $mu = new Mutuelle();

                    $mu->setNom($nom);

                    $object = $mu;
                    $status = true;
                    $message = 'Mutuelle bien rajoutée ';
                } catch (\Exception $ex) {
                    $status = false;
                    $message = $ex->getMessage();
                }

                break;


            case 'o':

                try {
                    $origine = $form['origine'];

                    $o = new Origine();

                    $o->setOrigine($origine);

                    $object = $o;
                    $status = true;
                    $message = 'Oriigine bien rajoutée ';
                } catch (\Exception $ex) {
                    $status = false;
                    $message = $ex->getMessage();
                }

                break;

            case 'v':

                try {
                    $ville = $form['ville'];

                    $v = new Ville();

                    $v->setVille($ville);

                    $object = $v;
                    $status = true;
                    $message = 'Ville bien rajoutée ';
                } catch (\Exception $ex) {
                    $status = false;
                    $message = $ex->getMessage();
                }

                break;

            default:

                break;

        }

        $entityManager->persist($object);

        $entityManager->flush();

        return new JsonResponse(array('success' => $status, 'message' => $message));
    }


    /**
     * @Route("/admin/affectation/show",name="admin_affectation_show")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function adminShowAffectation()
    {
        $natures = $this->getDoctrine()->getRepository(TypeExamen::class)->findAll();
        $examens = $this->getDoctrine()->getRepository(Examen::class)->findAll();
        return new JsonResponse(array('content' => $this->render(
            'content/admin/affectation.html.twig',
            ['natures' => $natures, 'examens' => $examens]
        )->getContent()));
    }

    /**
     * @Route("/admin/affectation/set",name="admin_affectation_set")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function adminAffectationSet(Request $request)
    {
        $nature = $request->get('nature');

        $examens = $this->getDoctrine()->getRepository(Examen::class)->findAll();

        return new JsonResponse(array('content' => $this->render('content/admin/affectation-table.html.twig', ['examens' => $examens, 'selected' => $nature])->getContent()));
    }

    /**
     * @Route("/admin/affectation/save",name="admin_affectation_save")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function adminAffectationSave(Request $request)
    {
        $nature = $request->get('nature');
        $examens = $request->get('examens');

        $entityManager = $this->getDoctrine()->getManager();

        $nature = $this->getDoctrine()->getRepository(TypeExamen::class)->find($nature);
        if (!$nature) {
            return new JsonResponse(['success' => false, 'message' => 'Nature non trouvée']);
        }

        if ($examens && is_array($examens)) {
            $examens = $this->getDoctrine()->getRepository(Examen::class)->findBy(['id' => $examens]);
            foreach ($examens as $e) {
                $e->setType($nature);
                $entityManager->persist($e);
            }
        }

        try {
            $entityManager->flush();
            return new JsonResponse(array('success' => true, 'message' => 'affectation bien enregistrée '));
        } catch (\Exception $ex) {
            return new JsonResponse(array('success' => false, 'message' => $ex->getMessage()));
        }
    }


    /**
     * @Route("/admin/update",name="admin_form_update")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function formUpdate(Request $request)
    {
        $form = $request->request->all()['form'];

        $type = $form['class'];
        $id = $form['get'];

        $entityManager = $this->getDoctrine()->getManager();

        switch ($type) {

            case 'p':
                try {
                    $nom = $form['nom'];
                    $prenom = $form['prenom'];
                    $cin = $form['cin'];
                    $adresse = $form['adresse'];
                    $sexe = $form['sexe'];
                    $situation = $form['situation'];
                    $tel = $form['tel'];
                    $date_naiss = $form['date_naiss'];
                    $ville = $form['ville'];
                    $mutuelle = $form['mutuelle'];

                    $p = $this->getDoctrine()->getRepository(Patient::class)->find($id);

                    $lVille = $this->getDoctrine()->getRepository(Ville::class)->find($ville);
                    $lMutuelle = $this->getDoctrine()->getRepository(Mutuelle::class)->find($mutuelle);

                    $p->setNom($nom)
                        ->setVille($lVille)
                        ->setMutuelle($lMutuelle)
                        ->setPrenom($prenom)
                        ->setAdresse($adresse)
                        ->setCin($cin)
                        ->setDateNaiss(new DateTime($date_naiss))
                        ->setSexe($sexe)
                        ->setSituation($situation)
                        ->setTel($tel);

                    $entityManager->persist($p);

                    $entityManager->flush();

                    return new JsonResponse(array('success' => true, 'message' => 'Patient bien modifié '));
                } catch (\Exception $ex) {
                    return new JsonResponse(array('success' => false, 'message' => $ex->getMessage()));
                }

                break;

            case 'm':

                try {
                    $nom = $form['nom'];
                    $prenom = $form['prenom'];
                    $tel = $form['tel'];
                    $email = $form['email'];
                    $specialite = $form['specialite'];

                    $m = $this->getDoctrine()->getRepository(Medecin::class)->find($id);

                    $m->setTel($tel)
                        ->setPrenom($prenom)
                        ->setNom($nom)
                        ->setEmail($email)
                        ->setSpecialite($specialite);

                    $entityManager->persist($m);

                    $entityManager->flush();

                    return new JsonResponse(array('success' => true, 'message' => 'Medecin bien modifié '));
                } catch (\Exception $ex) {
                    return new JsonResponse(array('success' => false, 'message' => $ex->getMessage()));
                }

                break;

            case 'n':

                try {
                    $nom = $form['nom'];

                    $n = $this->getDoctrine()->getRepository(TypeExamen::class)->find($id);

                    $n->setNom($nom);

                    $entityManager->persist($n);

                    $entityManager->flush();

                    return new JsonResponse(array('success' => true, 'message' => 'Nature bien modifié '));
                } catch (\Exception $ex) {
                    return new JsonResponse(array('success' => false, 'message' => $ex->getMessage()));
                }

                break;

            case 'e':

                try {
                    $nom = $form['nom'];
                    $prix = $form['prix'];
                    $type = $form['type'];

                    $e = $this->getDoctrine()->getRepository(Examen::class)->find($id);

                    $type = $this->getDoctrine()->getRepository(TypeExamen::class)->find($type);

                    $e->setNom($nom)
                        ->setPrix($prix)
                        ->setType($type);

                    $entityManager->persist($e);

                    $entityManager->flush();

                    return new JsonResponse(array('success' => true, 'message' => 'Examen bien modifié '));
                } catch (\Exception $ex) {
                    return new JsonResponse(array('success' => false, 'message' => $ex->getMessage()));
                }

                break;

            case 'mu':

                try {
                    $nom = $form['nom'];

                    $mu = $this->getDoctrine()->getRepository(Mutuelle::class)->find($id);

                    $mu->setNom($nom);

                    $entityManager->persist($mu);

                    $entityManager->flush();

                    return new JsonResponse(array('success' => true, 'message' => 'Mutuelle bien modifié '));
                } catch (\Exception $ex) {
                    return new JsonResponse(array('success' => false, 'message' => $ex->getMessage()));
                }


            case 'o':

                try {
                    $origine = $form['origine'];

                    $o = $this->getDoctrine()->getRepository(Origine::class)->find($id);

                    $o->setOrigine($origine);

                    $entityManager->persist($o);

                    $entityManager->flush();

                    return new JsonResponse(array('success' => true, 'message' => 'Origine bien modifié '));
                } catch (\Exception $ex) {
                    return new JsonResponse(array('success' => false, 'message' => $ex->getMessage()));
                }

                break;

            case 'v':

                try {
                    $ville = $form['ville'];

                    $v = $this->getDoctrine()->getRepository(Ville::class)->find($id);

                    $v->setVille($ville);

                    $entityManager->persist($v);

                    $entityManager->flush();

                    return new JsonResponse(array('success' => true, 'message' => 'Ville bien modifié '));
                } catch (\Exception $ex) {
                    return new JsonResponse(array('success' => false, 'message' => $ex->getMessage()));
                }

            default:

                break;

        }
    }

    /************************/

    /**
     * @Route("/api/facture/show",name="api_afficherFactures")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function api_afficherFactures(Request $request)
    {
        $id = $request->get('id');
        $facture = $this->getDoctrine()->getRepository(FactureConsultationExamen::class)->findOneBy(['consult_examen' => $id]);
        if ($facture) {
            $facture = $facture->getFacture()->getId();
            $ids = $this->getDoctrine()->getRepository(FactureConsultationExamen::class)->findBy(['facture' => $facture]);
            $facture = $this->getDoctrine()->getRepository(Facture::class)->find($facture);
            $total = 0;
            foreach ($ids as $id) {
                $conexam = $this->getDoctrine()->getRepository(ConsultationExamen::class)->find($id->getConsultExamen()->getId());
                $total += $conexam->getPrix();
                $patient = $conexam->getConsultation()->getPatient();
                $prestationsfactures[] = $conexam;
            }
            return new JsonResponse(array('content' => $this->render('rapport/facture-consultation.html.twig', ['prestationsfactures' => $prestationsfactures
                , 'patient' => $patient, 'facture' => $facture, 'total' => $total])->getContent()));
        }
    }

    /**
     * @Route("/api/facture/generate",name="api_facture_generate")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function api_factureGenerate(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $f = new Facture();
        $f->setNumFacture(uniqid() . "-" . date('Y'));
        $f->setDateFacture(new DateTime(strftime(date('Y-m-d'))));
        $entityManager->persist($f);
        $entityManager->flush();

        $ids = $request->get('id');
        if (is_array($ids)) {
            foreach ($ids as $id) {
                $fce = new FactureConsultationExamen();
                $ce = $this->getDoctrine()->getRepository(ConsultationExamen::class)->find($id);
                $fce->setFacture($f);
                $fce->setConsultExamen($ce);
                $entityManager->persist($fce);
            }
        } else {
            $fce = new FactureConsultationExamen();
            $ce = $this->getDoctrine()->getRepository(ConsultationExamen::class)->find($ids);
            $fce->setFacture($f);
            $fce->setConsultExamen($ce);

            $entityManager->persist($fce);
            $entityManager->flush();
        }

        return new JsonResponse(['success' => true], Response::HTTP_OK);
    }

    /**
     * @Route("/api/facture-consultation/generate",name="api_facture_generate_consultation")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function api_factureGenerateConsultation(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $ids = $request->get('ids');

        $f = new Facture();
        $f->setNumFacture(uniqid() . "-" . date('Y'));
        $f->setDateFacture(new DateTime(strftime(date('Y-m-d'))));
        $entityManager->persist($f);

        try {
            if (is_array($ids)) {
                foreach ($ids as $id) {
                    $fce = new FactureConsultationExamen();
                    $ce = $this->getDoctrine()->getRepository(ConsultationExamen::class)->find($id);
                    $fce->setFacture($f);
                    $fce->setConsultExamen($ce);

                    $entityManager->persist($fce);
                }
            } else {
                $fce = new FactureConsultationExamen();
                $ce = $this->getDoctrine()->getRepository(ConsultationExamen::class)->find($ids);
                $fce->setFacture($f);
                $fce->setConsultExamen($ce);

                $entityManager->persist($fce);
            }

            $entityManager->flush();

            return new JsonResponse(['success' => true], Response::HTTP_OK);
        } catch (\Exception $ex) {
            return new JsonResponse(['success' => false,'message'=>$ex->getMessage() ], Response::HTTP_OK);
        }
    }

    /*********** */

    /**
     * @Route("/api/prestations/RunWordDoc",name="RunWordDoc")
     * @Security("is_fully_authenticated()")
     * @return Response
     */
    public function RunWordDoc(Request $request)
    {
    }
}
