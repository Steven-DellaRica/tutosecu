describe('Json', () => {
    before(() => {
        cy.fixture("user.json").then(function (testData) {
            this.testData = testData.user
        })
    })

    // it("Boucle json", function () {
    //     this.testData.forEach((element) => {
    //         cy.log(element.name)
    //         cy.log(element.firstname)
    //         cy.log(element.email)
    //         cy.log(element.password)
    //     })
    // })

    it("Add new users", function () {
        this.testData.forEach((element) => {
            cy.log(element.firstname)

            cy.log(element.email)

            cy.visit('https://localhost:8000/register')
            cy.get('#register_firstname').type(element.firstname)
            cy.get('#register_name').type(element.name)
            cy.get('#register_email').type(element.email)
            cy.get('#register_password_first').type(element.password)
            cy.get('#register_password_second').type(element.password)
            cy.get('#register_submit').click()
            cy.get('strong').invoke("text").then((text => {
                if (text == `Le compte : ${element.email} existe déja`) {
                    cy.log("Doublon")
                } else {
                    cy.log("le compte a été ajouté en bdd")
                }
            }))

        })
    })
})