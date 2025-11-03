import React, { useState } from 'react';
function Notas() {
    // Estado único "formData" guarda todos os campos do formulário num objeto
    const [formData, setFormData] = useState({
        nome: '',
        email: '',
        telefone: '',
        morada: '',
        mensagem: ''
    });
    // Guarda os dados submetidos (para mostrar depois do envio)
    const [dadosSubmetidos, setDadosSubmetidos] = useState(null);
    // Esta função é chamada quando o formulário é submetido
    function handleSubmit(e) {
        e.preventDefault(); // impede o recarregamento da página
        setDadosSubmetidos(formData); // guarda os dados preenchidos
    }
    // Limpa o formulário e os dados apresentados
    function limparFormulario() {
        setFormData({ nome: '', email: '', telefone: '', morada: '', mensagem: '' });
        setDadosSubmetidos(null);
    }
    return (


        <div >
            <h2 className="mb-4">Cálculo da nota final</h2>
            <h4>Dados do Aluno</h4>

            <form>

                <div className="form-row mt-3">
                    <div className="col-md-8">
                        <label for="nome">Nome do aluno:</label>
                        <input type="text" className="form-control" id="nome" placeholder="Nome do aluno"/>
                    </div>
                    <div className="col-md-4">
                        <label for="disciplina">Disciplina:</label>
                        <input type="text" className="form-control" id="disciplina" placeholder="Disciplina"/>
                    </div>
                </div>

                <div className="form-row mt-3">
                    <div className="col-md-3">
                        <label for="notaTestes">Nota dos Testes:</label>
                        <input type="number" className="form-control" id="notaTestes" placeholder="Nota"/>
                    </div>
                    <div className="col-md-3">
                        <label for="percTestes">(% Testes):</label>
                        <input type="number" className="form-control" id="percTestes" placeholder="%"/>
                    </div>
                </div>

                <div className="form-row mt-3">
                    <div className="col-md-3">
                        <label for="notaTrabalhos">Nota dos Trabalhos:</label>
                        <input type="number" className="form-control" id="notaTrabalhos" placeholder="Nota"/>
                    </div>
                    <div className="col-md-3">
                        <label for="percTrabalhos">(% Trabalhos):</label>
                        <input type="number" className="form-control" id="percTrabalhos" placeholder="%"/>
                    </div>
                </div>


                <div className="form-row mt-3">
                    <div className="col-md-3">
                        <label for="notaAtitudes">Nota das Atitudes:</label>
                        <input type="number" className="form-control" id="notaAtitudes" placeholder="Nota"/>
                    </div>
                    <div className="col-md-3">
                        <label for="percAtitudes">(% Atitudes):</label>
                        <input type="number" className="form-control" id="percAtitudes" placeholder="%"/>
                    </div>
                </div>
                <button type="submit" className="btn btn-primary mt-4">Calcular</button>
            </form>
        </div>









    );
}
export default Notas;