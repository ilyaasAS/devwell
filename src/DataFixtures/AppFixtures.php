<?php

namespace App\DataFixtures;

use App\Document\AvisClient;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly DocumentManager $documentManager,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $categories = $this->loadCategories($manager);
        $this->loadProducts($manager, $categories);
        $this->loadUsers($manager);

        $manager->flush();

        $this->loadAvisClients();
        $this->documentManager->flush();
    }

    /**
     * @return Category[]
     */
    private function loadCategories(ObjectManager $manager): array
    {
        $categoryNames = ['Santé', 'High-Tech', 'Bien-être'];
        $categories = [];

        foreach ($categoryNames as $name) {
            $category = new Category();
            $category->setName($name);
            $manager->persist($category);
            $categories[] = $category;
        }

        return $categories;
    }

    /**
     * @param Category[] $categories
     */
    private function loadProducts(ObjectManager $manager, array $categories): void
    {
        if (\count($categories) < 3) {
            return;
        }

        $productsByCategory = [
            'Santé' => [
                ['name' => 'Compléments vitamines D3', 'price' => 14.90, 'description' => 'Complément alimentaire pour renforcer les défenses immunitaires.'],
                ['name' => 'Huile essentielle lavande', 'price' => 9.50, 'description' => 'Huile essentielle 10 ml pour apaisement et sommeil.'],
                ['name' => 'Tensiomètre brassard digital', 'price' => 34.99, 'description' => 'Tensiomètre électronique avec écran LCD et mémoire.'],
                ['name' => 'Coussin cervical mémoire', 'price' => 42.00, 'description' => 'Coussin ergonomique pour un bon maintien cervical.'],
            ],
            'High-Tech' => [
                ['name' => 'Montre connectée santé', 'price' => 89.90, 'description' => 'Suivi fréquence cardiaque, sommeil et activité.'],
                ['name' => 'Tensiomètre connecté Bluetooth', 'price' => 59.99, 'description' => 'Envoi des mesures vers smartphone.'],
                ['name' => 'Purificateur d\'air HEPA', 'price' => 129.00, 'description' => 'Filtration des particules et allergènes.'],
                ['name' => 'Lampe luminothérapie 10000 lux', 'price' => 49.90, 'description' => 'Simulation lumière du jour contre la dépression saisonnière.'],
            ],
            'Bien-être' => [
                ['name' => 'Tapis d\'acupression', 'price' => 29.90, 'description' => 'Détente musculaire et circulation.'],
                ['name' => 'Diffuseur huiles essentielles', 'price' => 24.99, 'description' => 'Nébulisation à froid, lumière douce.'],
                ['name' => 'Couverture lestée 6 kg', 'price' => 79.00, 'description' => 'Apaisement et meilleur sommeil.'],
                ['name' => 'Roller de massage musculaire', 'price' => 19.90, 'description' => 'Auto-massage des jambes et du dos.'],
            ],
        ];

        foreach ($categories as $category) {
            $name = $category->getName();
            if (!isset($productsByCategory[$name])) {
                continue;
            }
            foreach ($productsByCategory[$name] as $data) {
                $product = new Product();
                $product->setName($data['name']);
                $product->setPrice($data['price']);
                $product->setStock(100);
                $product->setImage('placeholder.jpg');
                $product->setDescription($data['description']);
                $product->setCategory($category);
                $manager->persist($product);
            }
        }
    }

    private function loadUsers(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setFirstName('Admin');
        $admin->setLastName('Devwell');
        $admin->setEmail('admin@devwell.com');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, '123456789'));
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        $testUsers = [
            ['firstName' => 'Jean', 'lastName' => 'Dupont', 'email' => 'user1@test.com'],
            ['firstName' => 'Marie', 'lastName' => 'Durant', 'email' => 'user2@test.com'],
            ['firstName' => 'Pierre', 'lastName' => 'Martin', 'email' => 'user3@test.com'],
        ];

        foreach ($testUsers as $data) {
            $user = new User();
            $user->setFirstName($data['firstName']);
            $user->setLastName($data['lastName']);
            $user->setEmail($data['email']);
            $user->setPassword($this->passwordHasher->hashPassword($user, '123456789'));
            $user->setRoles([]);
            $manager->persist($user);
        }
    }

    private function loadAvisClients(): void
    {
        $avis = [
            ['nom' => 'Sophie L.', 'email' => 'sophie.leclerc@email.com', 'note' => 5, 'commentaire' => 'Service impeccable et produits de qualité. Je recommande vivement !'],
            ['nom' => 'Thomas B.', 'email' => 'thomas.bernard@email.com', 'note' => 5, 'commentaire' => 'Livraison rapide, site clair. Mon tensiomètre connecté est parfait.'],
            ['nom' => 'Claire M.', 'email' => 'claire.martin@email.com', 'note' => 4, 'commentaire' => 'Très satisfaite de ma lampe luminothérapie. Un peu chère mais efficace.'],
            ['nom' => 'Marc D.', 'email' => 'marc.dubois@email.com', 'note' => 5, 'commentaire' => 'Meilleure expérience d\'achat bien-être en ligne. À refaire.'],
            ['nom' => 'Julie R.', 'email' => 'julie.rousseau@email.com', 'note' => 4, 'commentaire' => 'Bons conseils et suivi client au top. Produits conformes.'],
        ];

        foreach ($avis as $data) {
            $doc = new AvisClient();
            $doc->setNom($data['nom']);
            $doc->setEmail($data['email']);
            $doc->setNote($data['note']);
            $doc->setCommentaire($data['commentaire']);
            $this->documentManager->persist($doc);
        }
    }
}
