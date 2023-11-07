describe('test et boucle', ()=>{
    it('boucle v1', ()=>{
        //Répéter une action plusieurs fois
        for (let i = 0; i < 10; i++) {
            cy.visit('www.google.fr')            
        }
    })
    //Répéter un test plusieurs fois
    for (let i = 0; i < 10; i++) {
        it('boucle v2', ()=>{
            cy.visit('www.google.fr')
        });
        
    }
})