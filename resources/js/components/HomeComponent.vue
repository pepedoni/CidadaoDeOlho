<template>
   <div> 
       <v-card class="text-xs-center">

            <v-card-text >
                    <div class="card-body">
                        <v-card-title primary-title class="layout justify-center">
                            <h2>Cidadão de Olho</h2>
                        </v-card-title>
                        <h3 class="text-center">Autor: Pedro Moutinho</h3>
                        <p class="text-home">
                            <b>Essa aplicação foi criada no intuito de disponibilizar uma ferramenta para os cidadãos de Minas Gerais se informarem 
                            sobre os gastos com verbas indenizatórias dos deputados do seu estado. Além disso, a aplicação disponibiliza um estudo
                            sobre qual a rede social de maior impacto para divulgação dos dados.</b>
                        </p>
                        <p class="card-text">
                            <v-btn href="https://github.com/pepedoni/CidadaoDeOlho" target="_blank" text dark>Repositório no Github</v-btn>
                        </p>
                        <v-card-actions primary-title class="layout justify-center">
                            <v-btn @click="$router.push('verbas_indenizatorias')" text dark>Deputados Por Gastos com Verbas Indenizatorias</v-btn>
                            <v-btn @click="$router.push('redes_sociais')" text dark>Redes Sociais Mais Utilizadas</v-btn>
                        </v-card-actions>
                    </div>
            </v-card-text>
       </v-card>
   </div>
  
</template>