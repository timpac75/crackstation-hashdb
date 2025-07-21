*
while (($line = fgets($fh)) !== false) {
    $word = rtrim($line, "\r\n");

    // words.txt must be in sorted order for this to work!
    $count = 1;
    while (($line = fgets($fh)) !== false) {
        if ($hasher->hash(rtrim($line, "\r\n"), false) !== $hasher->hash($word, false)) {
            fseek($fh, -1 * strlen($line), SEEK_CUR);
            break;
        }
        $count++;
    }

    // Full match.
    $to_crack = $hasher->hash($word, false);
    $results = $lookup->crack($to_crack);
    if (count($results) !== $count || $results[0]->getPlaintext() !== "$word" || $results[0]->isFullMatch() !== true) {
        echo "FAILURE: Expected to crack [$word] but did not.\n";
        exit(1);
    } else {
        $cracked = $results[0]->getPlaintext();
        echo "Successfully cracked [$cracked].\n";
    }

    foreach ($results as $result) {
        if ($result->getAlgorithmName() !== $hash_algorithm) {
            echo "Algorithm name is not set correctly (full match).";
            exit(1);
        }
    }

    // Partial match (first 8 bytes, 16 hex chars).
    $to_crack = substr($to_crack, 0, 16);
    $results = $lookup->crack($to_crack);

    if (count($results) !== $count || $results[0]->getPlaintext() !== "$word" || $results[0]->isFullMatch() !== false) {
        echo "FAILURE: Expected to crack [$word] (as partial match) but did not.\n";
        exit(1);
    } else {
        $cracked = $results[0]->getPlaintext();
        echo "Successfully cracked [$cracked] (as partial match).\n";
    }

    foreach ($results as $result) {
        if ($result->getAlgorithmName() !== $hash_algorithm) {
            echo "Algorithm name is not set correctly (partial match).";
            exit(1);
        }
    }

}

fclose($fh);

?>
