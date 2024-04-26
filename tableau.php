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
            color: #333;
            text-align: center;
            text-decoration: underline;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 8px;
            text-align: center;
        }

        .resultat {
            font-size: 18px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>3.Tableau :</h2>
        
        <?php
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // Utilisez isset pour vérifier si les valeurs existent
            $objectif = isset($_POST["objectif"]) ? $_POST["objectif"] : null;
            $contraintes_input = isset($_POST["contraintes"]) ? $_POST["contraintes"] : null;

            // Vérifiez que les contraintes ne sont pas vides
            if ($contraintes_input !== null) {
                $contraintes = explode("\n", trim($contraintes_input)); // Séparer les contraintes par ligne
            } else {
                echo "Aucune contrainte reçue.";
                return;
            }

            if ($objectif === null) {
                echo "Aucun objectif reçu.";
                return;
            }

            // Fonction pour générer les noms des variables
            function generate_variable_names($constraints, $objective) {
                $variables = [];
                preg_match_all('/[a-zA-Z]+/', implode(" ", $constraints) . " " . $objective, $matches);
                $variables = array_unique($matches[0]); // Supprime les doublons
                sort($variables); // Tri alphabétique des variables
                return $variables;
            }

            // Générer les noms des variables
            $variables = generate_variable_names($contraintes, $objectif);

            // Ajouter les variables d'écart
            for ($i = 1; $i <= count($contraintes); $i++) {
                $variables[] = "e$i";
            }

            // Générer la matrice des contraintes
            function generate_constraints_matrix($constraints, $variables) {
                $matrix = [];
                foreach ($constraints as $index => $constraint) {
                    preg_match_all('/(\d+)([a-zA-Z]+)/', $constraint, $matches);
                    $coefficients = array_combine($matches[2], $matches[1]);

                    $row = [];
                    foreach ($variables as $var) {
                        $row[] = isset($coefficients[$var]) ? (int)$coefficients[$var] : 0;
                    }

                    // Ajouter le terme constant
                    preg_match('/[-+]?\d+$/', $constraint, $constant_match);
                    if (isset($constant_match[0])) {
                        $row[] = (int)$constant_match[0];
                    } else {
                        $row[] = 0; // Par défaut, 0 si pas de terme constant
                    }

                    $matrix[] = $row;
                }
                return $matrix;
            }

            // Créer la matrice des contraintes
            $matrix = generate_constraints_matrix($contraintes, $variables);

            // Générer la ligne pour l'équation économique
            function generate_objective_row($objectif, $variables) {
                preg_match_all('/(\d+)([a-zA-Z]+)/', $objectif, $matches);
                $coefficients = array_combine($matches[2], $matches[1]);

                $row = [];
                foreach ($variables as $var) {
                    $row[] = isset($coefficients[$var]) ? (int)$coefficients[$var] : 0;
                }

                return $row;
            }

            $objective_row = generate_objective_row($objectif, $variables);

            // Afficher la matrice sous forme de tableau HTML
            echo "<table border='1'>";

            // Afficher l'en-tête des variables
            echo "<tr><th>Variable</th>"; // Première cellule vide
            foreach ($variables as $var) {
                echo "<th>$var</th>";
            }
            echo "<th>Terme constant</th>";
            echo "</tr>";

            // Afficher les lignes de la matrice
            foreach ($matrix as $index => $row) {
                echo "<tr><td>Contrainte " . ($index + 1) . "</td>";
                foreach ($row as $value) {
                    echo "<td>$value</td>";
                }
                echo "</tr>";
            }

            // Afficher la ligne de l'équation économique
            echo "<tr><td>Équation économique</td>";
            foreach ($objective_row as $value) {
                echo "<td>$value</td>";
            }
            echo "<td>0</td>"; // Pas de terme constant dans l'équation économique
            echo "</tr>";

            echo "</table>";
        } else {
            echo "Aucune donnée reçue.";
        }
        ?>
    </div>
</body>
</html>