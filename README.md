# Covid-19 nas microrregiões

Este é o código fonte da [reportagem]() que analisa a evolução dos casos e mortes por covid-19 no Brasil.

O código-fonte e os dados estão sob licença GNU e CC-SA, respectivamente. O Estadão reserva os direitos sobre o texto jornalístico do material, incluindo a análise disponível no arquivo `pages/page/page.json`.

Os dados desta matéria foram compilados pela equipe de voluntários do [Brasil.io](https://brasil.io). No diretório `dist/data/cases` está uma cópia destes dados, baixada no dia 12.04.2020.

Em `dist/data/ibge` encontram-se dados sobre a divisão territorial do Brasil feita pelo IBGE.

Em `dist/data/data-scripts` está o script em Python que processa os dados do Brasil.io, salvando o resultado em `dist/data/processed`.

O diretório `dist/include` inclui blocos de HTML que são inseridos no conteúdo e que são necessários para gerar os gráficos.

Em `dist/scripts` está o script que gera os gráficos, feito em JavaScript/D3.js. O gráfico é estilizado pelo arquivo CSS disponível em `dist/styles`.

Para reproduzir este material em seu computador, clone este repositório e rode um servidor PHP local dentro do diretório dist. 