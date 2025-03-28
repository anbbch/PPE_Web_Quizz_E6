const quizData = {
    Mathématiques: {
        time: 20, // Temps global en secondes pour les mathématiques
        questions: [
            { question: "Quelle est la valeur de π ?", options: ["3", "3.14", "3.1415", "3.14159"], answer: "3.14159", type: "single" },
            { question: "Résultat de 2 + 2 x 2 ?", options: ["6", "8", "4", "10"], answer: "6", type: "single" },
            { question: "Résultat de 12 ÷ 4 ?", options: ["3", "4", "6", "12"], answer: "3", type: "single" },
            { question: "Quelle est la racine carrée de 81 ?", options: ["8", "9", "10", "81"], answer: "9", type: "single" },
            { question: "Combien font 10² ?", options: ["10", "20", "100", "200"], answer: "100", type: "single" },
            { question: "Combien de côtés a un hexagone ?", options: ["4", "5", "6", "7"], answer: "6", type: "single" },
            { question: "Résultat de 5 x 7 ?", options: ["25", "30", "35", "40"], answer: "35", type: "single" },
            { question: "Quelle est la somme des angles d’un triangle ?", options: ["90°", "180°", "270°", "360°"], answer: "180°", type: "single" },
            { question: "Quelle est la valeur de √64 ?", options: ["6", "7", "8", "9"], answer: "8", type: "single" },
            { question: "Quelle est la valeur de 5³ ?", options: ["15", "25", "75", "125"], answer: "125", type: "single" }
        ],
    },
    Français:  {
        time: 25, // Temps global en secondes pour les mathématiques
        questions: [
            { question: "Quelle est la capitale de la France ?", options: ["Lyon", "Paris", "Marseille", "Nice"], answer: "Paris", type: "single" },
            { question: "Qui a écrit 'Les Misérables' ?", options: ["Molière", "Victor Hugo", "Balzac", "Zola"], answer: "Victor Hugo", type: "single" },
            { question: "Quelle est la forme correcte : « Il (voir) le film » ?", options: ["voit", "vois", "vue", "voyait"], answer: "voit", type: "single" },
            { question: "Quelle est la définition de l’allégorie ?", options: ["Figure de style", "Oiseau", "Répétition", "Figure géométrique"], answer: "Figure de style", type: "single" },
            { question: "Quel est le synonyme de 'rapide' ?", options: ["Lent", "Vite", "Tranquille", "Doux"], answer: "Vite", type: "single" },
            { question: "Combien y a-t-il de lettres dans l’alphabet français ?", options: ["24", "25", "26", "27"], answer: "26", type: "single" },
            { question: "Quel est le contraire de 'grand' ?", options: ["Petit", "Gros", "Haut", "Fort"], answer: "Petit", type: "single" },
            { question: "Quel genre est 'table' ?", options: ["Masculin", "Féminin"], answer: "Féminin", type: "single" },
            { question: "Quelle est la terminaison des verbes du premier groupe ?", options: ["-er", "-ir", "-oir", "-re"], answer: "-er", type: "single" },
            { question: "Quel est le passé composé du verbe 'aller' ?", options: ["Allé", "Allée", "Été", "Est allé"], answer: "Est allé", type: "single" }
        ],
    },
    Informatique:  {
        time: 35, // Temps global en secondes pour les mathématiques
        questions: [
            { question: "Que signifie HTML ?", options: ["Hyper Texte Markup Language", "Hyperlink Texte Markup Language", "Home Tool Markup Language", "Hyper Tool Markup Language"], answer: "Hyper Texte Markup Language", type: "single" },
            { question: "Quel langage est utilisé pour le développement web ?", options: ["Python", "JavaScript", "C++", "Java"], answer: "JavaScript", type: "single" },
            { question: "Que signifie CSS ?", options: ["Cascading Style Sheets", "Computer Style Sheets", "Creative Style Sheets", "Colorful Style Sheets"], answer: "Cascading Style Sheets", type: "single" },
            { question: "Lequel de ces éléments est un système de gestion de base de données ?", options: ["MySQL", "HTML", "CSS", "HTTP"], answer: "MySQL", type: "single" },
            { question: "Quelle est l'extension des fichiers JavaScript ?", options: [".java", ".js", ".jvs", ".jsx"], answer: ".js", type: "single" },
            { question: "Que signifie 'IDE' ?", options: ["Environnement de Développement Intégré", "Environnement de Développement Interactif", "Moteur de Développement Interne", "Environnement Intelligent de Débogage"], answer: "Environnement de Développement Intégré", type: "single" },
            { question: "Quelle entreprise a développé le système d'exploitation Windows ?", options: ["Apple", "Google", "Microsoft", "Linux Foundation"], answer: "Microsoft", type: "single" },
            { question: "Quel symbole est utilisé pour indiquer un ID en CSS ?", options: ["#", ".", "/", "@"], answer: "#", type: "single" },
            { question: "Quelle balise est utilisée pour créer un lien hypertexte en HTML ?", options: ["<a>", "<link>", "<href>", "<url>"], answer: "<a>", type: "single" },
            { question: "Quel protocole est utilisé pour transférer les pages web ?", options: ["FTP", "HTTP", "SMTP", "DNS"], answer: "HTTP", type: "single" }
        ],
    },
    Anglais:  {
        time: 25, // Temps global en secondes pour les mathématiques
        questions: [        
            { question: "What is the past tense of 'go'?", options: ["Went", "Goes", "Gone", "Going"], answer: "Went", type: "single" },
            { question: "What does 'apple' mean in French?", options: ["Orange", "Banana", "Pomme", "Poire"], answer: "Pomme", type: "single" },
            { question: "Choose the correct sentence:", options: ["She go to school.", "She goes to school.", "She going to school.", "She goed to school."], answer: "She goes to school.", type: "single" },
            { question: "What is the opposite of 'big'?", options: ["Large", "Small", "Tall", "Wide"], answer: "Small", type: "single" },
            { question: "What is the plural of 'child'?", options: ["Childs", "Childrens", "Children", "Childes"], answer: "Children", type: "single" },
            { question: "What is the synonym of 'beautiful'?", options: ["Ugly", "Pretty", "Bad", "Dirty"], answer: "Pretty", type: "single" },
            { question: "Complete the sentence: 'He ______ a book yesterday.'", options: ["read", "reads", "reading", "reed"], answer: "read", type: "single" },
            { question: "What is the capital of England?", options: ["Paris", "London", "Berlin", "Madrid"], answer: "London", type: "single" },
            { question: "What is the correct spelling?", options: ["Recieve", "Receive", "Receve", "Recive"], answer: "Receive", type: "single" },
            { question: "What is the past tense of 'eat'?", options: ["Eat", "Eated", "Ate", "Eaten"], answer: "Ate", type: "single" }
        ]
    },
};
