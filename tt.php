<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programme Linéaire</title>
    <style>
        body {
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
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
        <h2>Matrice des Contraintes</h2>
        
        <?php
        // Fonction qui génère l'équation économique
        function create_objective_equation($objectif) {
            $equation = [];
            preg_match_all('/(\d+)([a-zA-Z]+)/', trim($objectif), $matches);
            $coefficients = array_combine($matches[2], $matches[1]);
            foreach ($coefficients as $var => $coeff) {
                $equation[] = $coeff;
            }
            // Le terme constant pour Z est 0 car c'est une équation économique
            $equation[] = 0;
            return $equation;
        }

        // Fonction qui génère les équations avec variables d'écart
        function create_constraints_with_slack($contraintes) {
            $equations = [];
            $lines = explode("\n", trim($contraintes));
            foreach ($lines as $index => $line) {
                preg_match('/(.*)(<=|≤|<)(\d+)/', $line, $matches);
                $coeffs = [];
                preg_match_all('/(\d+)([a-zA-Z]+)/', trim($matches[1]), $matches2);
                $coefficients = array_combine($matches2[2], $matches2[1]);
                foreach ($coefficients as $coeff) {
                    $coeffs[] = $coeff;
                }
                // Ajouter le coefficient de la variable d'écart et le terme constant
                $coeffs[] = 1; // Variable d'écart
                $coeffs[] = trim($matches[3]); // Terme constant
                $equations[] = $coeffs;
            }
            return $equations;
        }

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $objectif = $_POST["objectif"];
            $contraintes = $_POST["contraintes"];
            
            $equation_economique = create_objective_equation($objectif);
            $equations = create_constraints_with_slack($contraintes);

            // Afficher la matrice des contraintes
            echo "<div class='resultat'><h3>Matrice des Contraintes :</h3>";
            echo "<table><tr>";
            
            // Afficher l'en-tête du tableau (les variables)
            foreach ($equations[0] as $index => $value) {
                echo "<th>Variable $index</th>";
            }
            echo "</tr>";

            // Afficher les équations comme des lignes dans le tableau
            foreach ($equations as $equation) {
                echo "<tr>";
                foreach ($equation as $value) {
                    echo "<td>$value</td>";
                }
                echo "</tr>";
            }

            // Afficher l'équation économique en haut du tableau
            echo "<tr><td colspan='" . count($equations[0]) . "'>Équation économique : ";
            echo "Z";
            foreach ($equation_economique as $index => $coeff) {
                if ($index < count($equations[0]) - 1) {
                    echo $coeff >= 0 ? " - $coeff" : " + " . abs($coeff);
                }
            }
            echo " = 0";
            echo "</td></tr>";
            echo "</table>";
            echo "</div>";
        }
        ?>
    </div>
</body>
</html>