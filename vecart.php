        

        <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programme Linéaire</title>
    <style>
        body {
            background-image: url('https://img.freepik.com/vecteurs-libre/arriere-plan-texture-aquarelle-vert-doux-moderne_1055-18276.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .form-group {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            max-width: 90%;
            text-align: center;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-top: 0;
            text-decoration: underline; /* Souligner le titre */
            font-family: Arial, sans-serif; /* Utiliser une autre police */
        }

        label {
            font-weight: bold;
            color: #555;
            display: block; /* Afficher chaque label sur une nouvelle ligne */
            margin-bottom: 5px; /* Ajouter un peu d'espacement en bas */
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
        }

        button[type="submit"] {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }

        h2 {
            color: #333;
            text-decoration: underline;
            text-align: center;
        }

        .resultat {
            font-size: 18px;
            margin-top: 20px;
        }
        h4 {
            color: #ff0000;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>2.Variable d'écart</h2>
    <h4>*Pour résoudre le programme linéaire il faut d'abord ajouter des variables d'écart aux contraintes d'inégalité pour les transformer en équations d'égalité*</h4>
    <form action="tableau.php" method="post" class="form-group">
        <?php
        // Fonction qui génère l'équation économique
        function create_objective_equation($objectif) {
            $equation = "Z  ";
            preg_match_all('/(\d+)([a-zA-Z]+)/', trim($objectif), $matches);
            $coefficients = array_combine($matches[2], $matches[1]);
            foreach ($coefficients as $var => $coeff) {
                $equation .= $coeff >= 0 ? " - $coeff$var" : " + " . abs($coeff) . "$var";
            }
            $equation .= " = 0";
            return $equation;
        }
// Fonction qui génère l'équation économique avec les variables d'écart
function create_objective_equation_with_slack($objectif, $num_slacks) {
    $equation = "Z";  // Initialisation
    preg_match_all('/(\d+)([a-zA-Z]+)/', trim($objectif), $matches);
    $coefficients = array_combine($matches[2], $matches[1]);
    
    foreach ($coefficients as $var => $coeff) {
        $equation .= " - $coeff$var";
    }

    // Ajouter les variables d'écart avec coefficient 0
    for ($i = 1; $i <= $num_slacks; $i++) {
        $equation .= " + 0e$i"; // Coefficient 0 pour les variables d'écart
    }

    $equation .= " = 0"; // Fermeture de l'équation
    return $equation;
}
        // Fonction qui génère les équations avec variables d'écart
        function create_constraints_with_slack($contraintes) {
            $equations = [];
            $lines = explode("\n", trim($contraintes));
            foreach ($lines as $index => $line) {
                preg_match('/(.*)(<=|<=|≤)(\d+)/', $line, $matches);
                $equation = trim($matches[1]) . " + e$index = " . trim($matches[3]);
                $equations[] = $equation;
            }
            return $equations;
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $objectif = $_POST["objectif"];
            $contraintes = $_POST["contraintes"];
            $equation_economique = create_objective_equation($objectif);
            $equations = create_constraints_with_slack($contraintes);

            // Afficher l'équation économique
            echo "<div class='resultat'><h3>Équation économique :</h3>$equation_economique</div>";

            // Afficher les contraintes avec les variables d'écart
            echo "<div class='resultat'><h3>Contraintes :</h3>";
            foreach ($equations as $equation) {
                echo "$equation<br>";
            }
            echo "</div>";
        }
        ?>
          <br><br><button type="submit">ETAPE 3</button>
    </div>
</body>
</html>