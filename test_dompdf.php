<?php
require __DIR__ . '/vendor/autoload.php';
use Dompdf\Dompdf;

if (class_exists('Dompdf\Dompdf')) {
    echo "Dompdf cargado correctamente ✅";
} else {
    echo "Dompdf NO cargado ❌";
}
