// Um componente React é uma função que devolve JSX (JavaScript + HTML)
function App() {
  return (
    <div className="container mt-4">
      <h1 className="text-center text-primary">Olá React!</h1>
      <p className="lead text-center">
        Este é o teu primeiro componente React.
      </p>
      {/* Evento onClick em React usa camelCase e chama uma função */}
      <button
        className="btn btn-success d-block mx-auto"
        onClick={() => alert('Olá, Pedro!')}
      >
        Clica-me!
      </button>
    </div>
  );
}
export default App;