import React, { useState } from 'react';

export default function Notas() {

    const [formData, setFormData] = useState({
        nome: "",
        disciplina: "",
        notaTestes: "",
        percTestes: "",
        notaTrabalhos: "",
        percTrabalhos: "",
        notaAtitudes: "",
        percAtitudes: ""
    });

    const [dadosSubmetidos, setDadosSubmetidos] = useState(null); // dados preenchidos no último envio
    const [errors, setErrors] = useState([]); // lista de erros de validação
    const [resultado, setResultado] = useState(null); // resultado do cálculo da nota final

    function handleSubmit(e) {
        e.preventDefault(); // impede o recarregamento da página
        setErrors([]);
        setResultado(null);

        // converter valores para números
        const notaTestes = parseFloat(formData.notaTestes);
        const percTestes = parseFloat(formData.percTestes);
        const notaTrabalhos = parseFloat(formData.notaTrabalhos);
        const percTrabalhos = parseFloat(formData.percTrabalhos);
        const notaAtitudes = parseFloat(formData.notaAtitudes);
        const percAtitudes = parseFloat(formData.percAtitudes);

        
        if(notaTestes < 0 || notaTestes > 20) {
            setErrors([...errors, 'A nota dos testes tem de estar entre 0 e 20.']);
        }
        if(notaTrabalhos < 0 || notaTrabalhos > 20) {
            setErrors([...errors, 'A nota dos trabalhos tem de estar entre 0 e 20.']);
        }
        
        

        // soma das percentagens = 100 (com tolerância)
        if (isFinite(percTestes) && isFinite(percTrabalhos) && isFinite(percAtitudes)) {
            const soma = percTestes + percTrabalhos + percAtitudes;
            if (Math.abs(soma - 100) > 0.0001) {
                setErrors([...errors, 'A soma das percentagens tem de ser 100%.']);
                
            }
        }

        if (errors.length > 0) {
            
            setDadosSubmetidos(formData);
            return;
        }

        // cálculo da nota final
        const notaFinal = (notaTestes * percTestes / 100) + (notaTrabalhos * percTrabalhos / 100) + (notaAtitudes * percAtitudes / 100);
        const aprovado = notaFinal >= 10;
        setResultado({ notaFinal: Number(notaFinal.toFixed(2)), aprovado });
        setDadosSubmetidos(formData);
    }

    return (
        <div>
            <h1>Cálculo da nota final</h1>
            <p>Dados do Aluno</p>
            <form onSubmit={handleSubmit}>
                <div className='row'>
                    <div className='col-8'>
                        <div className='form-Group'>
                            <label>Nome</label>
                            <input type="text" className="form-control" value={formData.nome} onChange={(e) => setFormData({ ...formData, nome: e.target.value })} required />
                        </div>
                    </div>
                    <div className='col-4'>
                        <div className='form-Group'>
                            <label>Disciplina</label>
                            <input type="text" className="form-control" value={formData.disciplina} onChange={(e) => setFormData({ ...formData, disciplina: e.target.value })} required />
                        </div>
                    </div>
                </div>

                <div className='row'>
                    <div className='col-3'>
                            <div className='form-Group'>
                                <label>Nota dos testes:</label>
                                <input type="text" min="0" className="form-control" value={formData.notaTestes} onChange={(e) => setFormData({ ...formData, notaTestes: e.target.value })} required />
                            </div>
                        </div>
                    <div className='col-3'>
                        <div className='form-Group'>
                            <label>(%) Testes</label>
                            <input type="text" min="0" max="100" className="form-control" value={formData.percTestes} onChange={(e) => setFormData({ ...formData, percTestes: e.target.value })} required />
                        </div>
                    </div>
                </div>

                <div className='row'>
                    <div className='col-3'>
                        <div className='form-Group'>
                            <label>Nota dos trabalhos:</label>
                            <input type="text" min="0" max="20" className="form-control" value={formData.notaTrabalhos} onChange={(e) => setFormData({ ...formData, notaTrabalhos: e.target.value })} required />
                        </div>
                    </div>
                    <div className='col-3'>
                        <div className='form-Group'>
                            <label>(%) Trabalhos</label>
                            <input type="text" min="0" max="100" className="form-control" value={formData.percTrabalhos} onChange={(e) => setFormData({ ...formData, percTrabalhos: e.target.value })} required />
                        </div>
                    </div>
                </div>

                <div className='row'>
                    <div className='col-3'>
                        <div className='form-Group'>
                            <label>Nota das Atitudes:</label>
                            <input type="text" min="0" max="20" className="form-control" value={formData.notaAtitudes} onChange={(e) => setFormData({ ...formData, notaAtitudes: e.target.value })} required />
                        </div>
                    </div>
                    <div className='col-3'>
                        <div className='form-Group'>
                            <label>(%) Atitudes</label>
                            <input type="text" min="0" max="100" className="form-control" value={formData.percAtitudes} onChange={(e) => setFormData({ ...formData, percAtitudes: e.target.value })} required />
                        </div>
                    </div>
                </div>

                <div className="row">
                    <div className='col'>
                        <button className="btn btn-success mr-2">Enviar</button>
                    </div>
                </div>
            </form >
            
            {/* Mostrar erros se existirem */}
            {errors && errors.length > 0 && ( 
                <div className="alert alert-danger mt-3" role="alert">
                    <ul className="mb-0">
                        {errors.map((err, idx) => (<li key={idx}>{err}</li>))}
                    </ul>
                </div>
            )}

            {/* Mostrar resultado se existir */}
            {resultado && (
                <div className="card mt-3">
                    <div className="card-body">
                        <h5 className="card-title">Resultado</h5>
                        <p><strong>Nome:</strong> {dadosSubmetidos?.nome}</p>
                        <p><strong>Disciplina:</strong> {dadosSubmetidos?.disciplina}</p>
                        <p><strong>Nota Final:</strong> {resultado.notaFinal}</p>
                        <p><strong>Situação:</strong> {resultado.aprovado ? 'APROVADO' : 'REPROVADO'}</p>
                    </div>
                </div>
            )}
        </div >
    );
}