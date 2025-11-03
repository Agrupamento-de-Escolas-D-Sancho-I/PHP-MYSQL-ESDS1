// Um componente pode receber "props" (dados enviados pelo componente pai)
function Alerta(props) {
// Aqui usamos props.texto, enviada pelo componente App
if (props.tipo == "success")
return (
    <div className="alert alert-success">
        {props.texto}
    </div>
);    

if (props.tipo == "danger")
return (
    <div className="alert alert-danger">
        {props.texto}
    </div>
);    

if (props.tipo == "warning")
return (
    <div className="alert alert-warning">
        {props.texto}
    </div>
);    

return (
    <div className="alert alert-dark">
        {props.texto}
    </div>
);    


}
export default Alerta;