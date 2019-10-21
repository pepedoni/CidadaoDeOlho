  
import HomeComponent from './components/HomeComponent.vue';
import VerbasIndenizatoriasDeputadoComponent from './components/VerbasIndenizatoriasDeputadoComponent.vue';
import RedesSocialsComponent from './components/RedesSocialsComponent.vue'

const routes = [
  {
      name: 'home',
      path: '/',
      component: HomeComponent
  },
  {
      name: 'verbas_indenizatorias',
      path: '/verbas_indenizatorias',
      component: VerbasIndenizatoriasDeputadoComponent
  },
  {
    name: 'redes_sociais',
    path: '/redes_sociais',
    component: RedesSocialsComponent
  }

];

export default routes;