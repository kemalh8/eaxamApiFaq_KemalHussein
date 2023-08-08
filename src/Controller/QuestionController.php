<?php

namespace App\Controller;
use App\Form\QuestionType;
use App\Entity\Question;
use App\Repository\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

//#[Route('/questions')]
class QuestionController extends AbstractController
{
    private $serializer;
    private $entityManager;

    public function __construct(SerializerInterface $serializer, EntityManagerInterface $entityManager)
        {
            $this->serializer = $serializer;
            $this->entityManager = $entityManager;
        }

    #[Route('/questions', name: 'app_question', methods: ["GET"])]
    public function index(QuestionRepository $questionRepository): JsonResponse
        {
        $questions = $questionRepository->findAll();
        $serializedObject = json_decode($this->serializer->serialize($questions, 'json'));

        return $this->json($serializedObject);
        }


        #[Route('/questions/{question}', name: 'question_one', methods:["GET"])]
        public function getOne(Question $question){
            $serializedObject = json_decode($this->serializer->serialize($question, 'json'));
    
            return $this->json($serializedObject);
        }
    
        //#[IsGranted('ROLE_ADMIN')]
        #[Route('/questions/{question}', name: 'nft_delete', methods:["DELETE"])]
        public function deleteOne(Question $question, EntityManagerInterface $em){
            $em->remove($question);
            $em->flush();
    
            //pour créer un code de succes
            $response = new Response();
            $response->setStatusCode(204);
    
            return $response;
        }

        #[Route('/questions', name: 'app_question', methods:["POST"])]
        public function add(Request $request, EntityManagerInterface $em){
            $objectRequest = json_decode($request->getContent(), true);
                    $question = new Question();
                    $form = $this->createForm(QuestionType::class, $question);
                    $form->submit($objectRequest);
                    if($form->isValid()){
                        $em->persist($form->getData());
                        $em->flush();
    
                        $retour = json_decode($this->serializer->serialize($form, "json"));
                        $response = $this->json($retour);
                        $response->setStatusCode(201);
                        return $response;
                    } else{
                        $texErrors = json_decode($this->serializer->serialize($form, "json"));
                        $response = new JsonResponse($texErrors);
                        $response->setStatusCode(400);
                        return $response;
                    }
    
            }

            #[Route('/api/up/{id}', name: 'increment_score', methods: ['PATCH'])]
            public function incrementScore(Question $question): JsonResponse
            {
                $question->setScore($question->getScore() + 1);
                $this->entityManager->persist($question);
                $this->entityManager->flush();
        
                return $this->json(['message' => 'Score incremented successfully']);
            }


            
            #[Route('/api/down/{id}', name: 'decrement_score', methods: ['PATCH'])]
            public function decrementScore(Question $question): JsonResponse
            {
                $question->setScore($question->getScore() - 1);
                $this->entityManager->persist($question);
                $this->entityManager->flush();
        
                return $this->json(['message' => 'Score decremented successfully']);
            } 
        
        //#[IsGranted('ROLE_ADMIN')]
        #[Route('/questions/{question}', name: 'app_question_update', methods:["PUT"])]
        public function update(Question $question,Request $request, EntityManagerInterface $em){
           $objectRequest = json_decode($request->getContent(), true);
            
           $form = $this->createForm(QuestionType::class, $question);

           $form->submit($objectRequest);

           if($form->isValid()){
                $em->flush();
                $question = $form->getData();
                $questionSerialized = json_decode($this->serializer->serialize($question, "json"));
              
                return new JsonResponse($questionSerialized);
           }
           else{
                $response = $this->json([
                    "success" => false,
                    "message" => "bad request"
                ]);
                $response->setStatusCode(400);

                return $response;
           }
    
            
        }

    
}
