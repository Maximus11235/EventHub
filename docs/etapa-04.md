# Documentação da Etapa 04 — Interatividade com JavaScript
**Aplicação:** EventHub  
**Autor:** Maximus Rosa do Nascimento  
**Tag Git de Identificação:** `etapa-04`  

---

## 1. Descrição Geral das Funcionalidades Interativas

Nesta etapa, o projeto **EventHub** recebeu uma camada de interatividade desenvolvida com JavaScript nativo (ES6+), garantindo comportamento dinâmico e validações no lado do cliente sem a necessidade de recarregar páginas para obter respostas imediatas.

---

## 2. Detalhamento das Funcionalidades Desenvolvidas

### Funcionalidade 1: Validação Dinâmica do Formulário de Cadastro
* **Descrição:** Intercepta a submissão do formulário de criação de conta e valida os dados informados antes de enviar ao servidor.
* **Arquivos envolvidos:** `cadastro.php`, `script.js`, `style.css`
* **Funcionamento:** O script desativa os balões nativos do navegador (`novalidate`) e checa se todos os campos estão preenchidos, se o e-mail contém `@`, se o CPF possui exatamente 11 dígitos numéricos e se a senha coincide com a confirmação de senha.
* **Situações inválidas tratadas:** Em caso de erro, a submissão é bloqueada (`event.preventDefault()`) e um painel estilizado em vermelho (`.form-error-box`) é injetado no topo do formulário listando os problemas encontrados.

### Funcionalidade 2: Gestão Dinâmica e Recálculo do Carrinho de Compras
* **Descrição:** Permite adicionar ingressos a um carrinho real no `localStorage`, atualizar o número de itens no cabeçalho (*badge*), alterar quantidades e remover produtos com recálculo monetário em tempo real.
* **Arquivos envolvidos:** `detalhesIngresso.php`, `carrinho.php`, `script.js`, `style.css`
* **Funcionamento:** Na página de detalhes do ingresso, o envio do formulário captura os atributos `data-*` e salva o item no `localStorage`. Na página `carrinho.php`, os dados são lidos, convertidos em elementos HTML e exibidos na tela. Qualquer alteração de quantidade ou clique no botão de remoção (`✕`) recalcula o total dinamicamente.
* **Situações inválidas tratadas:** Se a quantidade digitada for inválida ou menor que 1, ela é corrigida para 1. Se todos os itens forem removidos, o sistema injeta dinamicamente o estado de "Carrinho de compras vazio". Ao reexibir a página ou utilizar o botão "Voltar" do navegador (`pageshow`), o estado do carrinho e do badge é sincronizado.

### Funcionalidade 3: Busca e Filtragem de Eventos em Tempo Real
* **Descrição:** Filtra os eventos exibidos na tela de resultados à medida que o usuário digita na barra de pesquisa no cabeçalho.
* **Arquivos envolvidos:** `busca.php`, `script.js`, `style.css`
* **Funcionamento:** Ouve o evento `input` na barra de pesquisa da página `busca.php` e compara o texto digitado exclusivamente com os títulos dos cards (`.ticket-title`). Cards que não correspondem têm seu estilo alterado para `display: none`.
* **Situações inválidas tratadas:** Atualiza dinamicamente um contador (`#search-counter`) acima dos resultados indicando o total de eventos encontrados. Caso nenhum evento corresponda, o contador informa que zero resultados foram encontrados para a consulta.

---

## 3. Matriz de Evidências

A tabela abaixo relaciona todos os requisitos obrigatórios da Etapa 04 às respetivas evidências no código-fonte e na interface da aplicação:

| Requisito | Funcionalidade relacionada | Arquivo(s) | Evidência |
| :--- | :--- | :--- | :--- |
| **Manipulação do DOM** | Injeção do painel de erros, criação dos itens do carrinho e mensagens de estado | `script.js`, `carrinho.php`, `cadastro.php` | `document.createElement()`, `insertBefore()`, `appendChild()`, `cartItemsList.innerHTML = ...` |
| **Tratamento de eventos** | Escuta de envios de formulário, digitação em tempo real, alterações de input e navegação | `script.js` | `addEventListener('submit')`, `addEventListener('input')`, `addEventListener('change')`, `addEventListener('pageshow')` |
| **Validação de formulários** | Validação customizada no cadastro com interrupção da submissão nativa | `script.js`, `cadastro.php` | Função de validação no formulário com atributo `novalidate`, checagem de e-mail (`@`), CPF (11 dígitos) e senhas |
| **Alteração dinâmica da interface** | Atualização do total do carrinho, badge do cabeçalho, visibilidade de cards e estados | `script.js`, `style.css` | `totalAmountSpan.textContent = ...`, `badge.textContent = ...`, `card.style.display = 'none'` |
| **Uso de funções** | Modularização da lógica em funções com responsabilidade única | `script.js` | `obterCarrinho()`, `guardarCarrinho()`, `atualizarBadgeCarrinho()`, `renderizarCarrinho()`, `executarBuscaResultados()` |
| **Uso de arrays** | Estruturação dos produtos do carrinho armazenados em memória | `script.js` | `carrinho.reduce()`, `carrinho.findIndex()`, `carrinho.push()`, `carrinho.splice()` |
| **Métodos de iteração** | Percorrimento de listas de itens do carrinho e cards de eventos para filtragem | `script.js` | Emprego dos métodos `.forEach()`, `.reduce()` e `.findIndex()` sobre arrays de dados e elementos |
| **Tratamento de situações inválidas** | Bloqueio de submissão incorreta, ajuste de inputs numéricos e estado de carrinho vazio | `script.js` | USO de `event.preventDefault()`, fallback para quantidade mínima (`< 1`), remoção de itens e mensagens de alerta visual |

---

## 4. Instruções de Execução e Teste

### Como Executar a Aplicação
1. Clone o repositório do projeto.
2. Certifique-se de que a tag `etapa-04` está ativa:
   ```bash
   git checkout etapa-04
3. Rode no terminal um localhost de sua preferência e abra o navegador:
    ```bash
    php -S localhost:8000