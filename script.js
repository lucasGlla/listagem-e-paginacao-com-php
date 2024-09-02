//listar chamados

const listarChamados = async (pagina) => {
    const dados = await fetch("./listagemChamado.php?pagina=" + pagina);
    const resposta = await dados.json();
    console.log(resposta);

    if(!resposta['status']){
        document.getElementById("msgAlerta").innerHTML = resposta['msg'];
    } else {
        const conteudo = document.querySelector(".listar-chamados");
        
        if (resposta['dados']) {
            conteudo.innerHTML = resposta['dados'];
        }
    }
}

listarChamados(1);
