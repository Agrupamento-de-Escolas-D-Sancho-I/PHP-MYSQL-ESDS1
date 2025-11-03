import { useState } from 'react';
// useState cria um "estado" dentro do componente.
// Quando o estado muda, o componente é renderizado novamente.
function Contador(props) {
const [contagem, setContagem] = useState(props.ini);
function aumentar() {
setContagem(contagem + 1);
}
function diminuir() {
if (contagem > 0) setContagem(contagem - 1);
}
function resetar() {
setContagem(0);
}
return (
<div className="text-center mt-4">
<h3>Contador: {contagem}</h3>
{/* Cada botão altera o estado de forma diferente */}
<button className="btn btn-primary mr-2" onClick={aumentar}>
+
</button>
<button className="btn btn-secondary mr-2" onClick={diminuir}>
-
</button>
<button className="btn btn-danger" onClick={resetar}>
Reset
</button>
</div>
);
}
export default Contador;