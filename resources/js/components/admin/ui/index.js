import AdminButton from './AdminButton.vue';
import AdminTable from './AdminTable.vue';
import AdminBadge from './AdminBadge.vue';
import AdminModal from './AdminModal.vue';
import AdminButtonGroup from './AdminButtonGroup.vue';
import AdminTabsLayout from '../AdminTabsLayout.vue';
import AdminTabPanel from '../AdminTabPanel.vue';

export {
  AdminButton,
  AdminTable,
  AdminBadge,
  AdminModal,
  AdminButtonGroup,
  AdminTabsLayout,
  AdminTabPanel
};

// Plugin do globalnej rejestracji komponentów
export default {
  install(app) {
    app.component('AdminButton', AdminButton);
    app.component('AdminTable', AdminTable);
    app.component('AdminBadge', AdminBadge);
    app.component('AdminModal', AdminModal);
    app.component('AdminButtonGroup', AdminButtonGroup);
    app.component('AdminTabsLayout', AdminTabsLayout);
    app.component('AdminTabPanel', AdminTabPanel);
  }
}; 