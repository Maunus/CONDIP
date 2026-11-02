<?PHP

function visInfTab($txt) {
//  echo '<img src="top.bmp" width="728px" height="15px" align="bottom" alt="Reklame" style="border: 4px solid #CCCCCC;">';
  $st = explode(chr(13), $txt);
  echo '<table width="655px">' . chr(13);
  foreach($st as $l) {
    $l = str_replace(chr(10), '', $l);
	echo '<tr><td>';
    echo str_replace(chr(9), '</td><td>', $l);
    echo '</td></tr>' . chr(13);
  }
  echo '</table>' . chr(13);
}

$infTextLis = "BANC	FOL 5	'Huehuetlatolli - Discursos en Mexicano'. 'The Bancroft Dialogues'
CAUB	FOL 1-80	Codex Aubin.
CF02	FOL 122, 123	Codex Florentino, Book 2. Bernardino de Sahagún.
CF11	FOL 7-9, 87-88	Codex Florentino, Book 11. Bernardino de Sahagún.
CM8B	FOL 293-294 (called fol. 3-4)	Códice Matritense de la Real Academia de la Historia, Bernardino de Sahagún.
LD21	FOL 1	Land document #21: Baltasar Esteban and wife sell or donate land to church, Coyoacan region, 1575.
MOLSA	FOL 1-20	Molina";

$infDictFor = "rel. noun	relational noun
zool.	zoological
bot.	botanic
min.	mineral
astr.	astronomical
obl. poss.	obligatorily possessed
tr 1	transitive with one obejct
tr 2	transitive with two objects
tr 3	transitive with three objects
tr 4	transitive with four objects
imp.agent	impersonal agent (impersonal verb)
imp.ag. 1	impersonal agent + 1 object (passive verb)
imp.ag. 2	impersonal agent + 2 objects (passive verb with 1 object)
imp.ag. 3	impersonal agent + 2 objects (passive verb with 2 objects)
R	reduplication";


$infProjDes = "AIM AND BASIC ORGANIZATION
CoNDiP is a tool for cracking the use and meaning of words in Nahuatl texts. 
It provides immediate connections between texts and words, and between words and roots.
It organizes the material, and facilitates studies of how words are used in texts, what words are formed with what roots, etc. It does not necessarily provide elegant text translations nor even adequate selections of glosses for roots and words.
CoNDiP organizes and suggests, but interpretation is restricted to an absolute minimum.
It is made up of three interconnected components: Root Dictionary, Word Dictionary, Text Component. 
Within the two Dictionary components the entities are word and root, respectively. 
In the text component the entity is text, divided into sentences.
You may look up a word or a root, or you may ask to have a text presented on the screen.
The interactiveness consists in the links between the components and in the effortless movement between them:
From a root entry in the Root Dictionary you may select a word containing the root and shift to its entry in the Word Dictionary. 
From a word entry in the Word Dictionary you may either select one of the roots contained in the word and go to its entry in the Root Dictionary, or you may select a sentence containing the word and go to the Text Component to see the text containing that sentence. 
In the Text Component you may select any word in any text and shift to the Word Dictionary to see that word's entry.
...
CoNDiP can exist only on computers: the entries in the two dictionary components and the texts are important, and they could be printed in three or more books, but it is the connections, the linking, between roots and words and between words in the Word Dictionary and word forms in the texts that make CoNDiP the tool it is; and those connections, that interactiveness, cannot be reproduced in a book.
...
It is our basic idea - and crucial for the success of the project - that anybody can contribute with a Nahuatl text that he or she has analyzed and translated. 
...";

$infNahuatl = "This dictionary describes Nahuatl as it is used in numerous Mesoamerican sources and codices from the 16th century. 
Nahuatl is well alive and is used by 1.5 million Mexicans today, mostly in central Mexico.";

?>
